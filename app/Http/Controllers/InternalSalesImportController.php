<?php

namespace App\Http\Controllers;

use App\Support\MaharaniSalesImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class InternalSalesImportController extends Controller
{
    public function create(Request $request): View
    {
        abort_if($request->routeIs('marketing.*'), 403);

        return view('internal.sales-import', array_merge(
            $this->buildContext($request),
            [
                'import_snapshot' => MaharaniSalesImporter::importedDataSnapshot(),
                'import_history' => MaharaniSalesImporter::importHistory(),
            ]
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if($request->routeIs('marketing.*'), 403);

        $context = $this->buildContext($request);

        $validated = $request->validate([
            'sales_workbook' => ['required', 'file', 'mimes:xlsx', 'max:10240'],
        ], [
            'sales_workbook.required' => 'File Excel penjualan wajib dipilih.',
            'sales_workbook.file' => 'Upload harus berupa file.',
            'sales_workbook.mimes' => 'File harus berformat .xlsx seperti workbook penjualan 2025.',
            'sales_workbook.max' => 'Ukuran file maksimal 10MB.',
        ]);

        $file = $validated['sales_workbook'];
        $storedPath = $file->store('imports/sales-workbooks', 'local');
        $absolutePath = Storage::disk('local')->path($storedPath);

        try {
            $stats = MaharaniSalesImporter::importSalesWorkbook(
                $absolutePath,
                (int) $request->user()->id,
                false,
                $file->getClientOriginalName()
            );
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route($context['route_prefix'] . '.imports.sales.create')
                ->withInput()
                ->withErrors([
                    'sales_workbook' => $this->friendlyImportError($exception),
                ]);
        } finally {
            Storage::disk('local')->delete($storedPath);
        }

        $successMessage = sprintf(
            'Import Excel selesai. %d mobil, %d order, dan %d pembayaran berhasil diproses. %d baris duplikat dilewati.',
            (int) ($stats['cars_created'] ?? 0),
            (int) ($stats['orders_created'] ?? 0),
            (int) ($stats['payments_created'] ?? 0),
            (int) ($stats['skipped'] ?? 0)
        );

        return redirect()
            ->route($context['route_prefix'] . '.imports.sales.create')
            ->with('success', $successMessage)
            ->with('import_stats', $stats)
            ->with('import_file_name', $file->getClientOriginalName());
    }

    public function destroy(Request $request): RedirectResponse
    {
        abort_if($request->routeIs('marketing.*'), 403);

        $context = $this->buildContext($request);
        $validated = $request->validate([
            'import_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'import_source' => ['nullable', 'string', 'max:255'],
        ]);
        $targetYear = isset($validated['import_year']) ? (int) $validated['import_year'] : null;
        $targetSource = isset($validated['import_source']) ? trim((string) $validated['import_source']) : null;
        $snapshot = MaharaniSalesImporter::importedDataSnapshotForScope($targetYear, $targetSource);

        if (array_sum($snapshot) === 0) {
            return redirect()
                ->route($context['route_prefix'] . '.imports.sales.create')
                ->withErrors([
                    'purge_import' => 'Belum ada data hasil import Excel pada target yang dipilih.',
                ]);
        }

        $deleted = MaharaniSalesImporter::purgeImportedSalesData($targetYear, $targetSource);
        $targetLabel = $this->buildPurgeTargetLabel($targetYear, $targetSource);

        $message = sprintf(
            'Data import Excel %s berhasil dibersihkan. %d mobil, %d order, %d pembayaran, %d customer import, %d penawaran arsip, dan %d test drive arsip telah dihapus.',
            $targetLabel,
            (int) ($deleted['cars'] ?? 0),
            (int) ($deleted['orders'] ?? 0),
            (int) ($deleted['payments'] ?? 0),
            (int) ($deleted['customers'] ?? 0),
            (int) ($deleted['offers'] ?? 0),
            (int) ($deleted['test_drives'] ?? 0)
        );

        return redirect()
            ->route($context['route_prefix'] . '.imports.sales.create')
            ->with('success', $message)
            ->with('purge_stats', $deleted);
    }

    /**
     * @return array{layout:string,pageTitle:string,title:string,route_prefix:string,workspace:string,accent:string}
     */
    private function buildContext(Request $request): array
    {
        if ($request->routeIs('marketing.*')) {
            return [
                'layout' => 'layouts.marketing',
                'pageTitle' => 'Import Excel Penjualan',
                'title' => 'Import Excel Penjualan',
                'route_prefix' => 'marketing',
                'workspace' => 'Marketing Workspace',
                'accent' => 'Marketing',
            ];
        }

        return [
            'layout' => 'layouts.supervisor',
            'pageTitle' => 'Import Excel Penjualan',
            'title' => 'Import Excel Penjualan',
            'route_prefix' => 'supervisor',
            'workspace' => 'Supervisor Workspace',
            'accent' => 'Supervisor',
        ];
    }

    private function friendlyImportError(\Throwable $exception): string
    {
        $message = trim($exception->getMessage());

        if ($message !== '') {
            return $message;
        }

        if ($exception instanceof RuntimeException) {
            return 'File Excel tidak dapat diproses. Pastikan struktur workbook sesuai data penjualan yang ingin diarsipkan.';
        }

        return 'Import gagal diproses. Silakan cek kembali file Excel yang diunggah.';
    }

    private function buildPurgeTargetLabel(?int $year, ?string $sourceName): string
    {
        if ($year && $sourceName) {
            return sprintf('untuk file %s tahun %d', $sourceName, $year);
        }

        if ($year) {
            return sprintf('untuk tahun %d', $year);
        }

        if ($sourceName) {
            return sprintf('untuk file %s', $sourceName);
        }

        return 'secara menyeluruh';
    }
}
