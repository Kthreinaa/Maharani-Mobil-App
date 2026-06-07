<?php

declare(strict_types=1);

$outputDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'bpmn';

if (!is_dir($outputDir) && !mkdir($outputDir, 0777, true) && !is_dir($outputDir)) {
    throw new RuntimeException('Gagal membuat folder output BPMN.');
}

final class SvgCanvas
{
    private array $elements = [];

    public function __construct(
        private int $width,
        private int $height,
        private string $title,
        private string $subtitle
    ) {
    }

    public function lane(int $y, int $height, string $label, string $fill = '#ffffff'): void
    {
        $this->elements[] = sprintf(
            '<rect x="36" y="%d" width="180" height="%d" rx="28" fill="#f8fafc" stroke="#cbd5e1" stroke-width="2"/>',
            $y,
            $height
        );
        $this->elements[] = sprintf(
            '<rect x="228" y="%d" width="%d" height="%d" rx="28" fill="%s" stroke="#cbd5e1" stroke-width="2"/>',
            $y,
            $this->width - 264,
            $height,
            $fill
        );
        $this->textBlock(126, $y + 48, $label, 24, '#0f172a', 800, 'middle', 30, 16);
    }

    public function event(int $cx, int $cy, string $label, bool $end = false): void
    {
        $stroke = $end ? '#0f172a' : '#1d4ed8';
        $this->elements[] = sprintf(
            '<circle cx="%d" cy="%d" r="24" fill="#ffffff" stroke="%s" stroke-width="%d"/>',
            $cx,
            $cy,
            $stroke,
            $end ? 5 : 3
        );
        $this->textBlock($cx, $cy + 58, $label, 16, '#334155', 700, 'middle', 18, 12);
    }

    public function task(int $x, int $y, int $w, int $h, string $title, string $subtitle = '', string $fill = '#ffffff', string $stroke = '#94a3b8'): void
    {
        $this->elements[] = sprintf(
            '<rect x="%d" y="%d" width="%d" height="%d" rx="22" fill="%s" stroke="%s" stroke-width="2"/>',
            $x,
            $y,
            $w,
            $h,
            $fill,
            $stroke
        );
        $this->textBlock($x + (int) ($w / 2), $y + 34, $title, 18, '#0f172a', 800, 'middle', 22, 18);
        if ($subtitle !== '') {
            $this->textBlock($x + (int) ($w / 2), $y + 68, $subtitle, 14, '#475569', 500, 'middle', 18, 26);
        }
    }

    public function doc(int $x, int $y, int $w, int $h, string $title, string $subtitle = ''): void
    {
        $path = sprintf(
            'M %1$d %2$d h %3$d l 16 16 v %4$d h -%5$d z',
            $x,
            $y,
            $w - 16,
            $h - 16,
            $w
        );
        $this->elements[] = sprintf('<path d="%s" fill="#fff7ed" stroke="#fb923c" stroke-width="2"/>', $path);
        $this->elements[] = sprintf('<path d="M %d %d h 16 v 16" fill="#fed7aa" stroke="#fb923c" stroke-width="2"/>', $x + $w - 16, $y);
        $this->textBlock($x + (int) ($w / 2), $y + 34, $title, 18, '#9a3412', 800, 'middle', 22, 18);
        if ($subtitle !== '') {
            $this->textBlock($x + (int) ($w / 2), $y + 68, $subtitle, 14, '#9a3412', 500, 'middle', 18, 26);
        }
    }

    public function gateway(int $cx, int $cy, string $label): void
    {
        $points = [
            [$cx, $cy - 34],
            [$cx + 34, $cy],
            [$cx, $cy + 34],
            [$cx - 34, $cy],
        ];
        $this->elements[] = sprintf(
            '<polygon points="%s" fill="#fef3c7" stroke="#d97706" stroke-width="3"/>',
            implode(' ', array_map(fn (array $point): string => $point[0] . ',' . $point[1], $points))
        );
        $this->textBlock($cx, $cy - 10, 'X', 22, '#92400e', 900, 'middle', 24, 12);
        $this->textBlock($cx, $cy + 58, $label, 16, '#92400e', 700, 'middle', 18, 14);
    }

    public function note(int $x, int $y, int $w, int $h, string $title, string $text): void
    {
        $this->elements[] = sprintf(
            '<rect x="%d" y="%d" width="%d" height="%d" rx="18" fill="#eff6ff" stroke="#93c5fd" stroke-width="2" stroke-dasharray="8 6"/>',
            $x,
            $y,
            $w,
            $h
        );
        $this->textBlock($x + 20, $y + 28, $title, 16, '#1d4ed8', 800, 'start', 20, 18);
        $this->textBlock($x + 20, $y + 56, $text, 13, '#1e3a8a', 500, 'start', 18, 28);
    }

    public function arrow(array $points, string $label = '', bool $dashed = false): void
    {
        if (count($points) < 2) {
            return;
        }

        $d = 'M ' . $points[0][0] . ' ' . $points[0][1];
        for ($i = 1; $i < count($points); $i++) {
            $d .= ' L ' . $points[$i][0] . ' ' . $points[$i][1];
        }

        $this->elements[] = sprintf(
            '<path d="%s" fill="none" stroke="#334155" stroke-width="3" marker-end="url(#arrowHead)" %s/>',
            $d,
            $dashed ? 'stroke-dasharray="8 6"' : ''
        );

        if ($label !== '') {
            $middleIndex = (int) floor((count($points) - 1) / 2);
            $mx = $points[$middleIndex][0];
            $my = $points[$middleIndex][1] - 12;
            $this->elements[] = sprintf(
                '<rect x="%d" y="%d" width="%d" height="24" rx="12" fill="#ffffff"/>',
                $mx - 48,
                $my - 14,
                max(96, strlen($label) * 7)
            );
            $this->textBlock($mx, $my + 4, $label, 13, '#334155', 700, 'middle', 16, 18);
        }
    }

    public function save(string $path): void
    {
        $title = htmlspecialchars($this->title, ENT_QUOTES, 'UTF-8');
        $subtitle = htmlspecialchars($this->subtitle, ENT_QUOTES, 'UTF-8');

        $svg = [];
        $svg[] = sprintf('<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d">', $this->width, $this->height, $this->width, $this->height);
        $svg[] = '<defs>';
        $svg[] = '<linearGradient id="headerGlow" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#08132e"/><stop offset="60%" stop-color="#102a63"/><stop offset="100%" stop-color="#f5a623"/></linearGradient>';
        $svg[] = '<marker id="arrowHead" markerWidth="10" markerHeight="10" refX="8" refY="5" orient="auto"><path d="M 0 0 L 10 5 L 0 10 z" fill="#334155"/></marker>';
        $svg[] = '<filter id="softShadow" x="-20%" y="-20%" width="140%" height="140%"><feDropShadow dx="0" dy="10" stdDeviation="14" flood-color="#0f172a" flood-opacity="0.12"/></filter>';
        $svg[] = '</defs>';
        $svg[] = sprintf('<rect width="%d" height="%d" fill="#f8fafc"/>', $this->width, $this->height);
        $svg[] = sprintf('<rect x="24" y="24" width="%d" height="82" rx="28" fill="url(#headerGlow)" filter="url(#softShadow)"/>', $this->width - 48);
        $svg[] = sprintf('<text x="%d" y="62" font-family="Inter, Arial, sans-serif" font-size="30" font-weight="800" fill="#ffffff" text-anchor="middle">%s</text>', (int) ($this->width / 2), $title);
        $svg[] = sprintf('<text x="%d" y="92" font-family="Inter, Arial, sans-serif" font-size="16" font-weight="500" fill="#dbeafe" text-anchor="middle">%s</text>', (int) ($this->width / 2), $subtitle);
        $svg = array_merge($svg, $this->elements);
        $svg[] = '</svg>';

        file_put_contents($path, implode("\n", $svg));
    }

    private function textBlock(int $x, int $y, string $text, int $size, string $fill, int $weight, string $anchor, int $lineHeight, int $maxChars): void
    {
        $lines = $this->wrap($text, $maxChars);
        foreach ($lines as $index => $line) {
            $this->elements[] = sprintf(
                '<text x="%d" y="%d" font-family="Inter, Arial, sans-serif" font-size="%d" font-weight="%d" fill="%s" text-anchor="%s">%s</text>',
                $x,
                $y + ($index * $lineHeight),
                $size,
                $weight,
                $fill,
                $anchor,
                htmlspecialchars($line, ENT_QUOTES, 'UTF-8')
            );
        }
    }

    private function wrap(string $text, int $maxChars): array
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;
            if (mb_strlen($candidate) > $maxChars && $current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines !== [] ? $lines : [''];
    }
}

function buildGuestDiagram(string $outputDir): void
{
    $svg = new SvgCanvas(
        1900,
        720,
        'BPMN 1 - Customer Guest / Prospek Awal',
        'Alur calon customer yang hanya melihat-lihat katalog dan belum berencana membeli mobil.'
    );

    $svg->lane(130, 150, 'Customer Guest', '#ffffff');
    $svg->lane(300, 150, 'Sistem Website', '#f8fbff');
    $svg->lane(470, 150, 'Marketing / CRM', '#ffffff');

    $svg->event(290, 205, 'Mulai');
    $svg->task(390, 155, 180, 86, 'Buka website', 'Masuk ke halaman home atau katalog');
    $svg->task(650, 155, 210, 86, 'Lihat katalog', 'Filter merek, harga, tahun, status unit');
    $svg->task(940, 155, 220, 86, 'Lihat detail mobil', 'Foto, spesifikasi, harga, deskripsi');
    $svg->gateway(1280, 198, 'Simpan favorit?');
    $svg->task(1380, 132, 220, 86, 'Simpan favorit', 'Unit disimpan untuk perbandingan ulang', '#ecfeff', '#67e8f9');
    $svg->task(1380, 238, 220, 86, 'Keluar dari website', 'Belum lanjut transaksi');

    $svg->task(440, 325, 220, 90, 'Catat aktivitas guest', 'Sistem menyimpan halaman dilihat, favorit, dan minat unit');
    $svg->task(760, 325, 240, 90, 'Buat status prospek awal', 'Belum membeli, tetapi masuk data CRM marketing');

    $svg->task(470, 500, 250, 90, 'Pantau sinyal minat', 'Marketing melihat unit yang sering dibuka atau difavoritkan', '#fefce8', '#facc15');
    $svg->gateway(850, 545, 'Perlu follow-up?');
    $svg->task(970, 480, 240, 90, 'Hubungi customer', 'Jika customer sudah punya akun atau kontak tersedia', '#fef2f2', '#fca5a5');
    $svg->task(970, 580, 240, 90, 'Simpan sebagai lead', 'Menunggu customer aktif kembali', '#eef2ff', '#a5b4fc');
    $svg->event(1330, 545, 'Selesai', true);

    $svg->arrow([[314, 205], [390, 205]]);
    $svg->arrow([[570, 205], [650, 205]]);
    $svg->arrow([[860, 205], [940, 205]]);
    $svg->arrow([[1160, 205], [1246, 205]]);
    $svg->arrow([[1314, 188], [1380, 175]], 'Ya');
    $svg->arrow([[1314, 208], [1380, 281]], 'Tidak');
    $svg->arrow([[1490, 218], [1490, 325], [660, 325]], 'aktivitas tersimpan');
    $svg->arrow([[1490, 324], [1490, 370], [1000, 370]]);
    $svg->arrow([[1000, 415], [1000, 545], [470, 545]]);
    $svg->arrow([[720, 545], [816, 545]]);
    $svg->arrow([[884, 525], [970, 525]], 'Ya');
    $svg->arrow([[884, 565], [970, 625]], 'Tidak');
    $svg->arrow([[1210, 525], [1330, 525], [1330, 545]]);
    $svg->arrow([[1210, 625], [1330, 625], [1330, 545]]);

    $svg->note(1500, 480, 310, 130, 'Output Sistem', 'Guest tidak langsung transaksi, tetapi aktivitasnya tetap masuk CRM sebagai prospek awal untuk tim marketing.');
    $svg->save($outputDir . DIRECTORY_SEPARATOR . '01-customer-guest.svg');
}

function buildOnlineNoTestDriveDiagram(string $outputDir): void
{
    $svg = new SvgCanvas(
        2400,
        1040,
        'BPMN 2 - Customer Online Tanpa Test Drive',
        'Alur pembelian online melalui sistem penawaran, tanpa appointment dan tanpa test drive.'
    );

    $svg->lane(130, 150, 'Customer Online', '#ffffff');
    $svg->lane(300, 150, 'Sistem Website', '#f8fbff');
    $svg->lane(470, 150, 'Marketing', '#ffffff');
    $svg->lane(640, 170, 'Supervisor', '#f8fbff');
    $svg->lane(830, 160, 'Dokumen / Serah Terima', '#ffffff');

    $svg->event(290, 205, 'Mulai');
    $svg->task(380, 155, 170, 86, 'Buka website', 'Home, katalog, dan detail unit');
    $svg->task(620, 155, 190, 86, 'Lihat katalog & detail', 'Customer membaca foto, spesifikasi, dan harga');
    $svg->task(890, 155, 200, 86, 'Chat / konsultasi', 'Tanya kondisi mobil ke marketing');
    $svg->task(1170, 155, 220, 86, 'Klik Tawar / Ajukan Nego', 'Masukkan nominal dan catatan untuk showroom');

    $svg->task(450, 325, 240, 90, 'Simpan riwayat penawaran', 'Sistem mencatat nominal, catatan, waktu, dan ronde negosiasi');
    $svg->task(790, 325, 240, 90, 'Kirim notifikasi penawaran', 'Supervisor menerima info ada tawaran baru');
    $svg->task(1120, 325, 220, 90, 'Tampilkan status penawaran', 'Customer dapat memantau pending / counter / accepted / rejected');

    $svg->gateway(620, 725, 'Respons supervisor?');
    $svg->task(520, 675, 210, 90, 'Terima harga', 'Supervisor setuju dengan harga customer', '#ecfdf5', '#86efac');
    $svg->task(860, 675, 210, 90, 'Tolak penawaran', 'Supervisor menghentikan negosiasi', '#fef2f2', '#fca5a5');
    $svg->task(1180, 675, 230, 90, 'Tawar balik', 'Supervisor kirim harga alternatif', '#eff6ff', '#93c5fd');

    $svg->gateway(1580, 720, 'Respons customer?');
    $svg->task(1490, 650, 220, 86, 'Setuju harga deal', 'Customer menerima counter offer', '#ecfdf5', '#86efac');
    $svg->task(1490, 760, 220, 86, 'Kirim tawaran baru', 'Customer ajukan harga baru lagi', '#eff6ff', '#93c5fd');
    $svg->task(1490, 870, 220, 86, 'Batalkan negosiasi', 'Customer berhenti bernegosiasi', '#fef2f2', '#fca5a5');

    $svg->task(1790, 650, 230, 90, 'Kunci harga final', 'Sistem menyimpan final price hasil kesepakatan', '#ecfeff', '#67e8f9');
    $svg->task(1790, 760, 230, 90, 'Checkout harga deal', 'Customer lanjut order dengan harga negosiasi');
    $svg->task(1790, 870, 230, 90, 'Transfer via sistem', 'Customer bayar online dan unggah bukti transfer');

    $svg->task(1140, 690, 250, 90, 'Verifikasi pembayaran', 'Supervisor cek bukti transfer lalu approve');
    $svg->doc(2080, 640, 250, 94, 'Faktur Pembelian', 'PDF otomatis setelah pembayaran terverifikasi');
    $svg->doc(2080, 752, 250, 94, 'Kwitansi Digital', 'Bukti bayar PDF, dapat dikirim email / diunduh');
    $svg->doc(2080, 864, 250, 94, 'BAST Kendaraan', 'Berita acara serah terima atau pengiriman unit');
    $svg->event(2360, 800, 'Selesai', true);

    $svg->task(340, 505, 250, 90, 'Tindak lanjut chat', 'Marketing menjelaskan kondisi unit, metode bayar, dan proses transaksi', '#fefce8', '#facc15');
    $svg->note(650, 495, 350, 110, 'Keunggulan Sistem Penawaran', 'Tawar-menawar tercatat otomatis, cepat, transparan, dan meminimalkan salah paham harga.');
    $svg->task(450, 875, 260, 90, 'Simpan alasan batal', 'Jika ditolak atau dibatalkan, alasan masuk CRM follow-up', '#eef2ff', '#a5b4fc');

    $svg->arrow([[314, 205], [380, 205]]);
    $svg->arrow([[550, 205], [620, 205]]);
    $svg->arrow([[810, 205], [890, 205]]);
    $svg->arrow([[1090, 205], [1170, 205]]);
    $svg->arrow([[1280, 241], [1280, 325], [690, 325]]);
    $svg->arrow([[690, 370], [790, 370]]);
    $svg->arrow([[1030, 370], [1120, 370]]);
    $svg->arrow([[1280, 415], [1280, 725], [654, 725]]);
    $svg->arrow([[654, 705], [730, 720]], 'accept');
    $svg->arrow([[654, 725], [860, 720]], 'reject');
    $svg->arrow([[654, 745], [1180, 720]], 'counter');
    $svg->arrow([[1285, 675], [1285, 370], [1340, 370]], 'status counter');
    $svg->arrow([[1410, 720], [1546, 720]]);
    $svg->arrow([[1614, 700], [1710, 693]], 'Setuju');
    $svg->arrow([[1614, 720], [1710, 803]], 'Tawar lagi');
    $svg->arrow([[1614, 740], [1710, 913]], 'Batal');
    $svg->arrow([[1710, 803], [1750, 803], [1750, 370], [690, 370]], 'ulang ke sistem');
    $svg->arrow([[730, 720], [1790, 695]], 'deal langsung');
    $svg->arrow([[1710, 693], [1790, 695]]);
    $svg->arrow([[2020, 695], [2020, 805], [1790, 805]]);
    $svg->arrow([[2020, 805], [1390, 805], [1390, 735]], 'proses bayar');
    $svg->arrow([[1140, 735], [1020, 735], [1020, 685]], 'bukti transfer');
    $svg->arrow([[1390, 735], [1140, 735]]);
    $svg->arrow([[1390, 915], [850, 915], [850, 920], [710, 920]], 'tidak deal');
    $svg->arrow([[1070, 720], [850, 720], [850, 920], [710, 920]]);
    $svg->arrow([[1390, 735], [1140, 735]]);
    $svg->arrow([[1390, 735], [1390, 685], [1490, 685]], 'status customer');
    $svg->arrow([[1390, 735], [1390, 803], [1490, 803]], '', true);
    $svg->arrow([[1390, 735], [1390, 913], [1490, 913]], '', true);
    $svg->arrow([[1390, 735], [1390, 370], [1340, 370]], '', true);
    $svg->arrow([[1390, 735], [1140, 735]]);
    $svg->arrow([[1390, 735], [1390, 685], [1490, 685]], '', true);
    $svg->arrow([[1390, 735], [1390, 803], [1490, 803]], '', true);
    $svg->arrow([[1390, 735], [1390, 913], [1490, 913]], '', true);
    $svg->arrow([[1390, 735], [1790, 695]]);
    $svg->arrow([[1390, 735], [1390, 370], [1340, 370]], '', true);
    $svg->arrow([[1390, 735], [1140, 735]]);
    $svg->arrow([[1390, 735], [1390, 685], [1490, 685]], '', true);
    $svg->arrow([[1390, 735], [1390, 803], [1490, 803]], '', true);
    $svg->arrow([[1390, 735], [1390, 913], [1490, 913]], '', true);
    $svg->arrow([[1390, 735], [1790, 695]]);
    $svg->arrow([[1390, 735], [1140, 735]]);
    $svg->arrow([[1390, 735], [1390, 685], [1490, 685]], '', true);
    $svg->arrow([[2020, 915], [2080, 915]]);
    $svg->arrow([[2020, 805], [2080, 799]]);
    $svg->arrow([[2020, 695], [2080, 687]]);
    $svg->arrow([[2330, 687], [2360, 687], [2360, 800]]);
    $svg->arrow([[2330, 799], [2360, 799]]);
    $svg->arrow([[2330, 911], [2360, 911], [2360, 800]]);
    $svg->save($outputDir . DIRECTORY_SEPARATOR . '02-online-tanpa-test-drive.svg');
}

function buildOnlineWithTestDriveDiagram(string $outputDir): void
{
    $svg = new SvgCanvas(
        2300,
        960,
        'BPMN 3 - Customer Online Dengan Test Drive',
        'Alur customer yang mencari mobil secara online, membuat janji test drive, lalu transaksi dilanjutkan di showroom namun tetap dicatat di sistem.'
    );

    $svg->lane(130, 150, 'Customer Online', '#ffffff');
    $svg->lane(300, 150, 'Sistem Website', '#f8fbff');
    $svg->lane(470, 150, 'Marketing', '#ffffff');
    $svg->lane(640, 160, 'Showroom / Supervisor', '#f8fbff');
    $svg->lane(820, 130, 'Dokumen & CRM', '#ffffff');

    $svg->event(290, 205, 'Mulai');
    $svg->task(380, 155, 170, 86, 'Buka website', 'Masuk ke katalog unit');
    $svg->task(620, 155, 200, 86, 'Pilih mobil', 'Lihat detail, foto, spesifikasi, harga');
    $svg->task(900, 155, 220, 86, 'Booking test drive', 'Isi tanggal, jam, dan catatan kebutuhan');
    $svg->task(420, 325, 250, 90, 'Simpan appointment', 'Sistem mencatat jadwal dan status booking');
    $svg->task(760, 325, 240, 90, 'Notifikasi ke marketing', 'Tim marketing menindaklanjuti permintaan test drive');
    $svg->task(1080, 325, 240, 90, 'Konfirmasi jadwal', 'Marketing menghubungi customer dan menyiapkan unit');

    $svg->task(410, 495, 250, 90, 'Datang ke showroom', 'Customer hadir sesuai appointment');
    $svg->task(760, 495, 220, 90, 'Test drive unit', 'Customer mencoba mobil secara langsung');
    $svg->task(1080, 495, 230, 90, 'Negosiasi di showroom', 'Harga, metode pembayaran, dan kesiapan unit dibahas langsung');
    $svg->gateway(1420, 540, 'Jadi beli?');
    $svg->task(1540, 470, 220, 90, 'Input transaksi ke sistem', 'Admin / supervisor mencatat transaksi hasil test drive', '#ecfdf5', '#86efac');
    $svg->task(1540, 590, 220, 90, 'Input status batal', 'Alasan batal dan hasil test drive tetap dicatat', '#fef2f2', '#fca5a5');

    $svg->task(1820, 470, 220, 90, 'Pilih metode bayar', 'Cash, transfer, atau kredit leasing');
    $svg->task(1820, 590, 220, 90, 'Follow-up marketing', 'Jadwalkan kontak ulang jika customer belum deal', '#eef2ff', '#a5b4fc');
    $svg->gateway(2090, 515, 'Approval supervisor');
    $svg->doc(1990, 720, 240, 88, 'Faktur + Kwitansi', 'PDF otomatis setelah transaksi disetujui');
    $svg->doc(1710, 720, 220, 88, 'BAST Kendaraan', 'Siap dicetak / diunduh saat serah terima');
    $svg->task(1460, 720, 190, 88, 'Serah terima unit', 'Unit diambil atau dikirim ke customer');
    $svg->event(2290, 764, 'Selesai', true);

    $svg->note(340, 820, 420, 90, 'Peran Sistem Setelah Test Drive', 'Walaupun customer sudah berada di showroom, transaksi tetap diinput ke sistem agar approval, dokumen, pembayaran, dan laporan tidak lagi manual.');

    $svg->arrow([[314, 205], [380, 205]]);
    $svg->arrow([[550, 205], [620, 205]]);
    $svg->arrow([[820, 205], [900, 205]]);
    $svg->arrow([[1010, 241], [1010, 325], [670, 325]]);
    $svg->arrow([[670, 370], [760, 370]]);
    $svg->arrow([[1000, 370], [1080, 370]]);
    $svg->arrow([[1200, 415], [1200, 495], [660, 495]]);
    $svg->arrow([[660, 540], [760, 540]]);
    $svg->arrow([[980, 540], [1080, 540]]);
    $svg->arrow([[1310, 540], [1386, 540]]);
    $svg->arrow([[1454, 520], [1540, 515]], 'Ya');
    $svg->arrow([[1454, 560], [1540, 635]], 'Tidak');
    $svg->arrow([[1760, 515], [1820, 515]]);
    $svg->arrow([[1760, 635], [1820, 635]]);
    $svg->arrow([[2040, 515], [2056, 515]]);
    $svg->arrow([[2140, 495], [2140, 764], [2050, 764]], 'disetujui');
    $svg->arrow([[2140, 535], [2140, 764], [1930, 764]], 'dokumen');
    $svg->arrow([[2140, 575], [2140, 635], [2040, 635]], 'butuh follow-up');
    $svg->arrow([[1930, 764], [1650, 764]]);
    $svg->arrow([[2230, 764], [2290, 764]]);
    $svg->arrow([[1650, 764], [1710, 764]]);
    $svg->save($outputDir . DIRECTORY_SEPARATOR . '03-online-dengan-test-drive.svg');
}

function buildOfflineDiagram(string $outputDir): void
{
    $svg = new SvgCanvas(
        2200,
        940,
        'BPMN 4 - Customer Offline Datang Langsung ke Showroom',
        'Alur customer yang tidak menggunakan website, tetapi seluruh data transaksi dan prospek tetap dicatat ke sistem.'
    );

    $svg->lane(130, 150, 'Customer Offline', '#ffffff');
    $svg->lane(300, 150, 'Marketing / Admin Showroom', '#f8fbff');
    $svg->lane(470, 150, 'Supervisor', '#ffffff');
    $svg->lane(640, 150, 'Sistem Maharani', '#f8fbff');
    $svg->lane(810, 110, 'Dokumen & Laporan', '#ffffff');

    $svg->event(290, 205, 'Mulai');
    $svg->task(380, 155, 180, 86, 'Datang ke showroom', 'Customer hadir langsung tanpa website');
    $svg->task(640, 155, 210, 86, 'Lihat unit fisik', 'Cek kondisi, interior, mesin, dan dokumen');
    $svg->task(930, 155, 210, 86, 'Konsultasi langsung', 'Marketing menjelaskan unit dan harga');
    $svg->gateway(1260, 198, 'Test drive?');
    $svg->task(1370, 132, 190, 86, 'Test drive', 'Customer mencoba unit di showroom', '#eff6ff', '#93c5fd');
    $svg->task(1370, 238, 190, 86, 'Langsung negosiasi', 'Tanpa test drive jika customer sudah yakin');

    $svg->task(410, 325, 250, 90, 'Input data customer', 'Nama, kontak, unit diminati, dan sumber transaksi offline');
    $svg->task(760, 325, 250, 90, 'Input hasil showroom', 'Hasil test drive, negosiasi, status prospek');
    $svg->gateway(1140, 370, 'Deal?');
    $svg->task(1260, 300, 220, 90, 'Input transaksi offline', 'Supervisor/admin mencatat order, harga final, dan channel offline', '#ecfdf5', '#86efac');
    $svg->task(1260, 420, 220, 90, 'Input alasan batal', 'Data batal tetap masuk sistem untuk follow-up', '#fef2f2', '#fca5a5');

    $svg->task(1560, 300, 220, 90, 'Verifikasi supervisor', 'Review data transaksi, pembayaran, dan kesiapan unit');
    $svg->task(1560, 420, 220, 90, 'Jadwalkan follow-up', 'Marketing menghubungi kembali sesuai alasan batal', '#eef2ff', '#a5b4fc');
    $svg->task(1840, 300, 200, 90, 'Pilih metode bayar', 'Cash, transfer, atau kredit leasing');
    $svg->doc(1810, 520, 250, 88, 'Invoice / Kwitansi', 'Dokumen digital bisa dicetak kapan saja');
    $svg->doc(1530, 520, 220, 88, 'BAST Kendaraan', 'Berita acara serah terima unit');
    $svg->task(1220, 520, 220, 88, 'Update dashboard', 'Penjualan offline masuk ke laporan owner');
    $svg->event(2130, 565, 'Selesai', true);

    $svg->note(340, 780, 420, 90, 'Inti Digitalisasi Offline', 'Walaupun customer tidak menggunakan website, showroom tetap menginput semuanya ke sistem agar tidak lagi memakai buku atau catatan manual terpisah.');

    $svg->arrow([[314, 205], [380, 205]]);
    $svg->arrow([[560, 205], [640, 205]]);
    $svg->arrow([[850, 205], [930, 205]]);
    $svg->arrow([[1140, 205], [1226, 205]]);
    $svg->arrow([[1294, 188], [1370, 175]], 'Ya');
    $svg->arrow([[1294, 208], [1370, 281]], 'Tidak');
    $svg->arrow([[1465, 324], [1465, 370], [660, 370]], 'hasil showroom');
    $svg->arrow([[660, 370], [760, 370]]);
    $svg->arrow([[1010, 370], [1106, 370]]);
    $svg->arrow([[1174, 350], [1260, 345]], 'Ya');
    $svg->arrow([[1174, 390], [1260, 465]], 'Tidak');
    $svg->arrow([[1480, 345], [1560, 345]]);
    $svg->arrow([[1480, 465], [1560, 465]]);
    $svg->arrow([[1780, 345], [1840, 345]]);
    $svg->arrow([[2040, 345], [2130, 345], [2130, 565]]);
    $svg->arrow([[1940, 565], [2130, 565]]);
    $svg->arrow([[1750, 565], [1810, 565]]);
    $svg->arrow([[1440, 565], [1530, 565]]);
    $svg->arrow([[1940, 345], [1940, 565]], 'dokumen');
    $svg->arrow([[1780, 465], [1780, 565], [1440, 565]], 'follow-up & arsip');
    $svg->save($outputDir . DIRECTORY_SEPARATOR . '04-customer-offline-showroom.svg');
}

function buildCrmDiagram(string $outputDir): void
{
    $svg = new SvgCanvas(
        2200,
        940,
        'BPMN 5 - CRM Follow-up, Approval Supervisor, dan Dashboard Owner',
        'Alur yang memastikan prospek, customer batal, pembayaran, dokumen, dan laporan tetap terhubung dalam satu sistem.'
    );

    $svg->lane(130, 150, 'Sumber Lead', '#ffffff');
    $svg->lane(300, 150, 'Marketing CRM', '#f8fbff');
    $svg->lane(470, 150, 'Supervisor', '#ffffff');
    $svg->lane(640, 150, 'Sistem / Dokumen', '#f8fbff');
    $svg->lane(810, 100, 'Owner Dashboard', '#ffffff');

    $svg->event(290, 205, 'Mulai');
    $svg->task(380, 155, 230, 86, 'Lead masuk', 'Guest, penawaran, test drive, atau customer batal');
    $svg->task(700, 155, 240, 86, 'Buat status prospek', 'Guest / booking / negosiasi / batal / deal');
    $svg->task(1030, 155, 230, 86, 'Jadwal follow-up', 'Tentukan kapan marketing harus menghubungi ulang');
    $svg->task(1360, 155, 220, 86, 'Hubungi customer', 'Chat, telepon, atau update melalui sistem');

    $svg->gateway(1710, 198, 'Ada progres?');
    $svg->task(1830, 130, 220, 86, 'Lanjut proses bisnis', 'Bisa ke test drive, penawaran, atau transaksi', '#ecfdf5', '#86efac');
    $svg->task(1830, 238, 220, 86, 'Tutup sebagai lost', 'Alasan batal tetap disimpan', '#fef2f2', '#fca5a5');

    $svg->task(430, 325, 250, 90, 'Cek pembayaran', 'Supervisor memverifikasi cash, transfer, atau kredit');
    $svg->gateway(820, 370, 'Valid?');
    $svg->task(940, 300, 250, 90, 'Approve transaksi', 'Supervisor menyetujui status transaksi dan final price', '#ecfdf5', '#86efac');
    $svg->task(940, 420, 250, 90, 'Minta perbaikan data', 'Bukti transfer atau data transaksi belum lengkap', '#fef2f2', '#fca5a5');

    $svg->doc(1280, 300, 240, 88, 'Faktur Otomatis', 'Terbit setelah transaksi / pembayaran tervalidasi');
    $svg->doc(1280, 408, 240, 88, 'Kwitansi Digital', 'Bukti bayar PDF, dapat diunduh / dicetak');
    $svg->doc(1280, 516, 240, 88, 'BAST Kendaraan', 'Dokumen serah terima unit');
    $svg->task(1610, 380, 250, 90, 'Sinkron ke dashboard', 'Update online/offline, metode bayar, status dokumen, alasan batal');

    $svg->task(470, 710, 250, 90, 'Lihat laporan penjualan', 'Owner memantau omzet, total transaksi, unit terjual');
    $svg->task(840, 710, 250, 90, 'Analisis alur customer', 'Online vs offline, dengan / tanpa test drive, conversion rate');
    $svg->task(1210, 710, 260, 90, 'Pantau performa CRM', 'Jumlah follow-up, prospek batal, alasan batal, status dokumen');
    $svg->event(1620, 755, 'Selesai', true);

    $svg->note(1670, 690, 450, 110, 'Keluaran Analitik Sistem', 'Dashboard owner menampilkan sumber transaksi online/offline, metode pembayaran, status penjualan, follow-up customer, dokumen, serta alasan batal untuk pengambilan keputusan.');

    $svg->arrow([[314, 205], [380, 205]]);
    $svg->arrow([[610, 205], [700, 205]]);
    $svg->arrow([[940, 205], [1030, 205]]);
    $svg->arrow([[1260, 205], [1360, 205]]);
    $svg->arrow([[1580, 205], [1676, 205]]);
    $svg->arrow([[1744, 188], [1830, 173]], 'Ya');
    $svg->arrow([[1744, 208], [1830, 281]], 'Tidak');
    $svg->arrow([[1940, 324], [1940, 370], [680, 370]], 'approval saat transaksi');
    $svg->arrow([[680, 370], [786, 370]]);
    $svg->arrow([[854, 350], [940, 345]], 'Ya');
    $svg->arrow([[854, 390], [940, 465]], 'Tidak');
    $svg->arrow([[1190, 345], [1280, 344]]);
    $svg->arrow([[1190, 345], [1280, 452]], '', true);
    $svg->arrow([[1190, 345], [1280, 560]], '', true);
    $svg->arrow([[1520, 344], [1610, 425]]);
    $svg->arrow([[1520, 452], [1610, 425]]);
    $svg->arrow([[1520, 560], [1610, 425]]);
    $svg->arrow([[1860, 425], [1960, 425], [1960, 755], [720, 755]]);
    $svg->arrow([[720, 755], [840, 755]]);
    $svg->arrow([[1090, 755], [1210, 755]]);
    $svg->arrow([[1470, 755], [1620, 755]]);
    $svg->save($outputDir . DIRECTORY_SEPARATOR . '05-crm-approval-dashboard.svg');
}

function buildOverviewDiagram(string $outputDir): void
{
    $svg = new SvgCanvas(
        2200,
        880,
        'BPMN 0 - Ringkasan Proses Bisnis Maharani Mobil',
        'Pemetaan alur besar sistem informasi penjualan dan CRM showroom mobil Maharani Mobil.'
    );

    $svg->lane(130, 150, 'Customer', '#ffffff');
    $svg->lane(300, 150, 'Marketing', '#f8fbff');
    $svg->lane(470, 150, 'Supervisor / Admin', '#ffffff');
    $svg->lane(640, 150, 'Sistem & Dokumen', '#f8fbff');
    $svg->lane(810, 100, 'Owner', '#ffffff');

    $svg->event(290, 205, 'Mulai');
    $svg->gateway(430, 205, 'Jenis customer');
    $svg->task(540, 138, 220, 86, 'Guest', 'Lihat katalog dan detail unit tanpa transaksi', '#eff6ff', '#93c5fd');
    $svg->task(540, 242, 220, 86, 'Online tanpa test drive', 'Nego via sistem lalu checkout', '#ecfdf5', '#86efac');
    $svg->task(810, 138, 220, 86, 'Online + test drive', 'Booking test drive lalu datang ke showroom', '#fefce8', '#facc15');
    $svg->task(810, 242, 220, 86, 'Offline showroom', 'Datang langsung lalu diinput internal', '#fef2f2', '#fca5a5');

    $svg->task(420, 335, 270, 90, 'Follow-up marketing', 'Prospek, customer batal, dan hasil negosiasi ditindaklanjuti');
    $svg->task(800, 335, 280, 90, 'Approval supervisor', 'Validasi transaksi, pembayaran, dan status unit');
    $svg->task(1190, 335, 280, 90, 'Dokumen digital otomatis', 'Faktur, kwitansi digital, dan BAST kendaraan');
    $svg->task(1580, 335, 260, 90, 'Laporan & dashboard', 'Online/offline, metode bayar, unit laku, alasan batal');
    $svg->event(1950, 380, 'Selesai', true);

    $svg->note(420, 670, 1360, 120, 'File BPMN Lengkap', 'Lihat diagram terpisah untuk detail lengkap: 01 Customer Guest, 02 Online Tanpa Test Drive, 03 Online Dengan Test Drive, 04 Offline Showroom, 05 CRM Approval & Dashboard.');

    $svg->arrow([[314, 205], [396, 205]]);
    $svg->arrow([[464, 185], [540, 181]], 'Guest');
    $svg->arrow([[464, 205], [540, 285]], 'Online no test drive');
    $svg->arrow([[464, 225], [810, 181]], 'Online + test drive');
    $svg->arrow([[464, 245], [810, 285]], 'Offline');
    $svg->arrow([[650, 224], [650, 380], [690, 380]]);
    $svg->arrow([[650, 328], [650, 380], [690, 380]]);
    $svg->arrow([[920, 224], [920, 380], [800, 380]]);
    $svg->arrow([[920, 328], [920, 380], [800, 380]]);
    $svg->arrow([[690, 380], [800, 380]]);
    $svg->arrow([[1080, 380], [1190, 380]]);
    $svg->arrow([[1470, 380], [1580, 380]]);
    $svg->arrow([[1840, 380], [1950, 380]]);
    $svg->save($outputDir . DIRECTORY_SEPARATOR . '00-ringkasan-proses-bisnis.svg');
}

function buildIndex(string $outputDir): void
{
    $files = [
        ['00-ringkasan-proses-bisnis.svg', 'Ringkasan proses bisnis keseluruhan sistem.'],
        ['01-customer-guest.svg', 'Alur customer guest yang hanya melihat-lihat katalog.'],
        ['02-online-tanpa-test-drive.svg', 'Alur negosiasi online tanpa test drive sampai pembayaran dan dokumen.'],
        ['03-online-dengan-test-drive.svg', 'Alur online dengan appointment test drive dan transaksi showroom yang tetap tercatat digital.'],
        ['04-customer-offline-showroom.svg', 'Alur customer offline yang datang langsung ke showroom.'],
        ['05-crm-approval-dashboard.svg', 'Alur CRM follow-up, approval supervisor, dokumen digital, dan dashboard owner.'],
    ];

    $cards = array_map(
        static fn (array $item): string => sprintf(
            '<article class="card"><h2>%s</h2><p>%s</p><div class="actions"><a href="%s" target="_blank">Buka SVG</a><a href="%s" download>Download SVG</a><a href="%s" target="_blank">Buka PNG</a><a href="%s" download>Download PNG</a></div><img src="%s" alt="%s"/></article>',
            htmlspecialchars($item[0], ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($item[1], ENT_QUOTES, 'UTF-8'),
            rawurlencode($item[0]),
            rawurlencode($item[0]),
            rawurlencode(str_replace('.svg', '.png', $item[0])),
            rawurlencode(str_replace('.svg', '.png', $item[0])),
            rawurlencode(str_replace('.svg', '.png', $item[0])),
            htmlspecialchars($item[1], ENT_QUOTES, 'UTF-8')
        ),
        $files
    );

    $html = <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BPMN Maharani Mobil</title>
  <style>
    body { margin: 0; font-family: Inter, Arial, sans-serif; background: linear-gradient(180deg, #eef4ff 0%, #ffffff 100%); color: #0f172a; }
    .wrap { max-width: 1440px; margin: 0 auto; padding: 32px 24px 56px; }
    .hero { background: linear-gradient(135deg, #08132e 0%, #102a63 58%, #f5a623 140%); color: white; border-radius: 28px; padding: 28px 32px; box-shadow: 0 24px 70px rgba(15, 23, 42, 0.18); }
    .hero h1 { margin: 0 0 10px; font-size: 34px; }
    .hero p { margin: 0; max-width: 920px; line-height: 1.7; color: #dbeafe; }
    .actions-top { margin-top: 18px; display: flex; gap: 12px; flex-wrap: wrap; }
    .actions-top a, .actions a { display: inline-flex; align-items: center; justify-content: center; padding: 12px 18px; border-radius: 999px; text-decoration: none; font-weight: 700; }
    .actions-top a:first-child { background: #ffffff; color: #08132e; }
    .actions-top a:last-child { background: rgba(255,255,255,0.14); color: #ffffff; border: 1px solid rgba(255,255,255,0.24); }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-top: 28px; }
    .card { background: rgba(255,255,255,0.86); border: 1px solid rgba(226,232,240,0.95); border-radius: 26px; padding: 20px; box-shadow: 0 24px 70px rgba(15, 23, 42, 0.08); }
    .card h2 { margin: 0 0 8px; font-size: 20px; }
    .card p { margin: 0 0 14px; color: #475569; line-height: 1.7; min-height: 56px; }
    .actions { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 14px; }
    .actions a:first-child { background: #08132e; color: white; }
    .actions a:last-child { background: #eff6ff; color: #1d4ed8; }
    img { width: 100%; border-radius: 18px; border: 1px solid #cbd5e1; background: white; }
  </style>
</head>
<body>
  <div class="wrap">
    <section class="hero">
      <h1>BPMN Maharani Mobil</h1>
      <p>Kumpulan diagram BPMN lengkap sesuai alur sistem informasi penjualan dan CRM showroom mobil Maharani Mobil. File tersedia dalam format SVG untuk kualitas tajam dan PNG untuk kebutuhan presentasi cepat.</p>
      <div class="actions-top">
        <a href="00-ringkasan-proses-bisnis.svg" target="_blank">Buka Ringkasan</a>
        <a href="bpmn-maharani-mobil.zip" download>Download Semua Diagram</a>
      </div>
    </section>
    <section class="grid">
      __CARDS__
    </section>
  </div>
</body>
</html>
HTML;

    file_put_contents($outputDir . DIRECTORY_SEPARATOR . 'index.html', str_replace('__CARDS__', implode("\n", $cards), $html));
}

buildOverviewDiagram($outputDir);
buildGuestDiagram($outputDir);
buildOnlineNoTestDriveDiagram($outputDir);
buildOnlineWithTestDriveDiagram($outputDir);
buildOfflineDiagram($outputDir);
buildCrmDiagram($outputDir);
buildIndex($outputDir);
