@php
  $carName = trim(($car->merk ?? '') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? ''));
  $unitCode = $car->kode_unit ?: 'Tanpa kode unit';
  $carImage = is_array($car->photos ?? null) && !empty($car->photos[0])
    ? asset('storage/' . $car->photos[0])
    : 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1400&auto=format&fit=crop';
  $minimumDpAmount = (float) $defaultSimulation['minimum_dp_amount'];
  $showsMinimumDpBadge = round((float) $defaultSimulation['dp_amount'], 2) <= round($minimumDpAmount, 2);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Simulasi Kredit | Maharani Mobil</title>
  <meta name="description" content="Pilih leasing Maharani Mobil dan simulasikan kredit mobil bekas sebelum pengajuan diteruskan."/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  <header class="bg-slate-50/70 dark:bg-slate-950/70 backdrop-blur-xl sticky top-0 z-50">
    <div class="flex justify-between items-center w-full px-6 py-4 max-w-screen-2xl mx-auto">
      <a class="text-2xl font-black text-[#1A2B4C] dark:text-white tracking-tighter font-headline" href="/">Maharani Mobil</a>
      <div class="flex items-center gap-4">
        @include('components.nav-tools')
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="px-4 py-2 text-slate-500 dark:text-slate-300 hover:text-primary">Logout</button>
        </form>
      </div>
    </div>
  </header>

  <main class="max-w-screen-2xl mx-auto w-full px-6 md:px-12 py-10 flex-grow">
    <div class="flex items-center justify-between gap-4 mb-8">
      <div>
        <p class="text-xs uppercase tracking-[0.24em] text-slate-400 font-semibold">Pembelian Kredit Maharani Mobil</p>
        <h1 class="text-4xl font-extrabold text-primary mt-2">Simulasi Kredit & Pilih Leasing</h1>
      </div>
      <a class="text-sm font-semibold text-primary hover:text-[#F5A623]" href="{{ route('checkout', ['car_id' => $car->id, 'offer_id' => $sourceOffer?->id]) }}">Kembali ke metode pembelian</a>
    </div>

    @if ($errors->any())
      <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        Mohon cek kembali pilihan leasing, DP, tenor, dan persetujuan pengajuan kredit Anda.
      </div>
    @endif

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-[minmax(0,1.55fr)_minmax(430px,0.95fr)]">
      <section class="rounded-[2rem] bg-white p-6 md:p-8 shadow-xl shadow-blue-900/5">
        <form id="credit-simulation-form" method="GET" action="{{ route('checkout.credit') }}" class="space-y-8">
          <input type="hidden" name="car_id" value="{{ $car->id }}"/>
          <input type="hidden" id="credit_scroll_position" name="scroll_position" value="{{ (int) request()->query('scroll_position', 0) }}"/>
          @if ($sourceOffer)
            <input type="hidden" name="offer_id" value="{{ $sourceOffer->id }}"/>
          @endif

          <div class="rounded-[1.6rem] border border-slate-100 p-6">
            <h2 class="text-[2rem] font-extrabold text-primary">1. Pilih Leasing</h2>
            <p class="mt-3 text-[15px] leading-7 text-on-surface-variant">
              Pengajuan kredit di sistem Maharani Mobil hanya untuk data pencatatan. Approval tetap dilakukan oleh leasing yang Anda pilih.
            </p>
            <div class="mt-6">
              <label class="mb-3 block text-sm font-bold text-primary" for="leasing_partner">Leasing Pilihan</label>
              <select id="leasing_partner" name="leasing_partner" class="w-full rounded-[1.2rem] border-slate-200 bg-white px-4 py-4 text-base">
                @foreach ($leasingPartners as $partner)
                  <option value="{{ $partner['code'] }}" @selected($selectedPartnerCode === $partner['code'])>{{ $partner['name'] }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="rounded-[1.6rem] border border-slate-100 p-6">
            <h2 class="text-[2rem] font-extrabold text-primary">2. Atur Rencana DP & Tenor</h2>
            <p class="mt-3 text-[15px] leading-7 text-on-surface-variant">
              Pilih tenor 36, 48, atau 60 bulan, lalu tekan tombol hitung simulasi untuk melihat estimasi cicilan per bulan.
            </p>

            <div class="mt-6 grid gap-6">
              <div>
                <div class="flex items-center justify-between gap-4">
                  <label class="text-sm font-bold text-primary" for="credit_dp_amount">Rencana DP</label>
                  <span class="text-sm font-semibold text-slate-500">Minimum {{ \App\Support\CurrencyFormatter::rupiah($defaultSimulation['minimum_dp_amount']) }}</span>
                </div>
                <div class="mt-3 flex flex-wrap gap-3">
                  @foreach ([20, 25, 30] as $quickPercent)
                    <button
                      class="inline-flex rounded-full border px-4 py-2 text-sm font-bold {{ round($selectedDpAmount) === round($cashPrice * ($quickPercent / 100)) ? 'border-primary bg-primary text-white' : 'border-slate-200 text-primary' }}"
                      type="submit"
                      name="quick_dp_percentage"
                      value="{{ $quickPercent }}"
                    >
                      {{ $quickPercent }}%
                    </button>
                  @endforeach
                </div>
                <div class="mt-4 grid gap-4 md:grid-cols-[minmax(0,1fr)_180px]">
                  <input
                    id="credit_dp_amount"
                    name="credit_dp_amount"
                    class="w-full rounded-[1.2rem] border-slate-200 bg-white px-4 py-4 text-base"
                    type="number"
                    step="1000"
                    min="{{ (int) ceil($defaultSimulation['minimum_dp_amount']) }}"
                    inputmode="numeric"
                    data-price="{{ (float) $cashPrice }}"
                    data-minimum-dp="{{ (float) $defaultSimulation['minimum_dp_amount'] }}"
                    value="{{ round($selectedDpAmount) }}"
                  />
                  <div class="rounded-[1.2rem] border border-slate-200 bg-slate-50 px-4 py-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Persentase DP</p>
                    <p id="credit-dp-percent-label" class="mt-2 text-xl font-extrabold text-primary">{{ number_format((float) $defaultSimulation['dp_percentage'], 1, ',', '.') }}%</p>
                  </div>
                </div>
                <p id="credit-dp-feedback" class="mt-3 text-sm {{ $selectedDpAmount < $defaultSimulation['minimum_dp_amount'] ? 'text-amber-700' : 'text-slate-500' }}">
                  Inputkan Nominal DP sesuai budget anda. Minimun DP {{ \App\Support\CurrencyFormatter::rupiah($defaultSimulation['minimum_dp_amount']) }}, 20 % dari harga unit.
                </p>
              </div>

              <div>
                <p class="text-sm font-bold text-primary">Tenor</p>
                <div class="mt-3 grid grid-cols-3 gap-3">
                  @foreach ($tenorOptions as $tenor)
                    <div>
                      <input
                        id="credit-tenor-{{ $tenor }}"
                        class="peer sr-only"
                        type="radio"
                        name="credit_tenor_months"
                        value="{{ $tenor }}"
                        @checked($selectedTenor === $tenor)
                      />
                      <label
                        for="credit-tenor-{{ $tenor }}"
                        data-credit-tenor-label
                        class="block cursor-pointer rounded-[1rem] border px-4 py-4 text-center text-base font-bold transition {{ $selectedTenor === $tenor ? 'border-primary bg-primary text-white shadow-lg shadow-blue-900/10' : 'border-slate-200 bg-white text-primary hover:border-primary/40' }}"
                      >
                        {{ $tenor }} Bulan
                      </label>
                    </div>
                  @endforeach
                </div>
              </div>

              <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <p class="text-sm font-medium text-slate-500">
                  Pilih leasing, isi DP, pilih tenor, lalu tekan tombol hitung simulasi.
                </p>
                <button
                  id="credit-calculate-button"
                  class="inline-flex items-center justify-center rounded-[1rem] bg-primary px-6 py-3.5 text-base font-bold text-white"
                  type="submit"
                >
                  Hitung Simulasi
                </button>
              </div>

              <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-[1.2rem] border border-slate-200 bg-slate-50 px-4 py-4">
                  <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Harga Mobil</p>
                  <p class="mt-2 text-base font-extrabold text-primary">{{ \App\Support\CurrencyFormatter::rupiah($cashPrice) }}</p>
                </div>
                <div class="rounded-[1.2rem] border border-slate-200 bg-slate-50 px-4 py-4">
                  <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Leasing Aktif</p>
                  <p class="mt-2 text-base font-extrabold text-primary">{{ $defaultSimulation['partner']['name'] }}</p>
                </div>
                <div class="rounded-[1.2rem] border border-slate-200 bg-slate-50 px-4 py-4">
                  <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Tenor Aktif</p>
                  <p class="mt-2 text-base font-extrabold text-primary">{{ $defaultSimulation['tenor_months'] }} bulan</p>
                </div>
              </div>
            </div>
          </div>
        </form>

        <div class="rounded-[1.6rem] border border-slate-100 p-6">
          <h2 class="text-[2rem] font-extrabold text-primary">3. Ringkasan Simulasi Kredit</h2>
          <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-[1.3rem] border border-slate-200 bg-slate-50 px-5 py-4">
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Harga Mobil</p>
              <p class="mt-2 text-lg font-extrabold text-primary">{{ \App\Support\CurrencyFormatter::rupiah($cashPrice) }}</p>
            </div>
            <div class="rounded-[1.3rem] border border-slate-200 bg-slate-50 px-5 py-4">
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Leasing Dipilih</p>
              <p class="mt-2 text-lg font-extrabold text-primary">{{ $defaultSimulation['partner']['name'] }}</p>
            </div>
            <div class="rounded-[1.3rem] border border-slate-200 bg-slate-50 px-5 py-4">
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Tenor</p>
              <p class="mt-2 text-lg font-extrabold text-primary">{{ $defaultSimulation['tenor_months'] }} bulan</p>
            </div>
            <div class="rounded-[1.3rem] border border-slate-200 bg-slate-50 px-5 py-4">
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">DP</p>
              <p class="mt-2 text-lg font-extrabold text-primary">{{ \App\Support\CurrencyFormatter::rupiah($defaultSimulation['dp_amount']) }}</p>
            </div>
            <div class="rounded-[1.3rem] border border-slate-200 bg-slate-50 px-5 py-4">
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Estimasi Cicilan / Bulan</p>
              <p class="mt-2 text-lg font-extrabold text-primary">{{ \App\Support\CurrencyFormatter::rupiah($defaultSimulation['monthly_installment']) }}</p>
            </div>
          </div>
        </div>

        <form id="credit-booking-form" method="POST" action="{{ route('customer.orders.store') }}" class="hidden">
          @csrf
          <input type="hidden" name="car_id" value="{{ $car->id }}"/>
          <input type="hidden" name="payment_method" value="credit"/>
          <input type="hidden" name="sales_flow" value="direct_purchase"/>
          @if ($sourceOffer)
            <input type="hidden" name="offer_id" value="{{ $sourceOffer->id }}"/>
          @endif
          <input id="credit-booking-leasing-partner" type="hidden" name="leasing_partner" value="{{ $selectedPartnerCode }}"/>
          <input id="credit-booking-dp-percentage" type="hidden" name="credit_dp_percentage" value="{{ $defaultSimulation['dp_percentage'] }}"/>
          <input id="credit-booking-dp-amount" type="hidden" name="credit_dp_amount" value="{{ $defaultSimulation['dp_amount'] }}"/>
          <input id="credit-booking-tenor-months" type="hidden" name="credit_tenor_months" value="{{ $defaultSimulation['tenor_months'] }}"/>
          <input id="credit-booking-monthly-installment" type="hidden" name="credit_monthly_installment" value="{{ $defaultSimulation['monthly_installment'] }}"/>
          <input id="credit-booking-interest-rate" type="hidden" name="credit_interest_rate" value="{{ $defaultSimulation['annual_rate'] }}"/>
        </form>
      </section>

      <aside class="w-full max-w-[520px] xl:justify-self-end">
        <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-blue-900/5">
          <div class="grid grid-cols-[112px_minmax(0,1fr)] items-start gap-5 p-5 md:grid-cols-[128px_minmax(0,1fr)] md:p-6">
            <img alt="{{ $carName }}" class="h-20 w-28 rounded-2xl object-cover md:h-24 md:w-32" src="{{ $carImage }}"/>
            <div class="min-w-0">
              <h2 class="text-[1.45rem] font-extrabold leading-tight text-primary md:text-[1.7rem]">{{ $carName }}</h2>
              <p class="mt-2 text-sm font-semibold text-slate-500">Kode Unit: {{ $unitCode }}</p>
              <p class="mt-3 text-[14px] leading-7 text-on-surface-variant">
                Simulasi ini membantu Anda memperkirakan DP dan cicilan sebelum pengajuan diteruskan oleh supervisor ke leasing yang dipilih.
              </p>
            </div>
          </div>

          <div class="border-t border-slate-200 px-5 py-5 md:px-6">
            <div class="space-y-5 text-primary">
              <div class="flex items-center justify-between gap-4 text-[1.05rem] font-bold md:text-[1.15rem]">
                <span>Harga Mobil</span>
                <span>{{ \App\Support\CurrencyFormatter::rupiah($cashPrice) }}</span>
              </div>
              <div class="flex items-center justify-between gap-4 border-t border-slate-200 pt-5 text-[1.05rem] font-bold md:text-[1.15rem]">
                <div>
                  <p id="credit-sidebar-dp-label">{{ $showsMinimumDpBadge ? 'DP Minimum' : 'DP' }}</p>
                  <span id="credit-sidebar-dp-badge" class="mt-2 {{ $showsMinimumDpBadge ? 'inline-flex' : 'hidden' }} rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700">20% wajib dibayar</span>
                </div>
                <span id="credit-sidebar-dp-amount">{{ \App\Support\CurrencyFormatter::rupiah($defaultSimulation['dp_amount']) }}</span>
              </div>
              <div class="flex items-center justify-between gap-4 border-t border-slate-200 pt-5 text-[1.05rem] font-bold md:text-[1.15rem]">
                <div>
                  <p>Estimasi Cicilan</p>
                  <span class="mt-2 inline-flex rounded-full bg-sky-100 px-3 py-1 text-sm font-semibold text-sky-700">{{ $defaultSimulation['tenor_months'] }} bulan</span>
                </div>
                <span>{{ \App\Support\CurrencyFormatter::rupiah($defaultSimulation['monthly_installment']) }}</span>
              </div>
            </div>

            <div class="mt-6 border-t border-slate-200 pt-6">
              <button
                class="inline-flex w-full items-center justify-center rounded-[1rem] bg-[#ffcf33] px-5 py-4 text-lg font-bold text-primary disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
                type="submit"
                form="credit-booking-form"
                id="credit-booking-submit"
              >
                Kirim Pengajuan Kredit
              </button>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </main>

  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin menanyakan simulasi kredit untuk unit ' . $carName . '.'])
  @include('components.ui-system-footer')
  <script>
    (function () {
      var simulationForm = document.getElementById('credit-simulation-form');
      var bookingForm = document.getElementById('credit-booking-form');
      var scrollPositionField = document.getElementById('credit_scroll_position');
      var savedScrollPosition = {{ (int) request()->query('scroll_position', 0) }};
      var tenorInputs = document.querySelectorAll('input[name="credit_tenor_months"]');
      var leasingField = document.getElementById('leasing_partner');
      var dpAmountField = document.getElementById('credit_dp_amount');
      var dpPercentLabel = document.getElementById('credit-dp-percent-label');
      var dpFeedback = document.getElementById('credit-dp-feedback');
      var bookingLeasingField = document.getElementById('credit-booking-leasing-partner');
      var bookingDpPercentageField = document.getElementById('credit-booking-dp-percentage');
      var bookingDpAmountField = document.getElementById('credit-booking-dp-amount');
      var bookingTenorField = document.getElementById('credit-booking-tenor-months');
      var bookingMonthlyInstallmentField = document.getElementById('credit-booking-monthly-installment');
      var bookingInterestRateField = document.getElementById('credit-booking-interest-rate');
      var sidebarDpLabel = document.getElementById('credit-sidebar-dp-label');
      var sidebarDpBadge = document.getElementById('credit-sidebar-dp-badge');
      var sidebarDpAmount = document.getElementById('credit-sidebar-dp-amount');
      var minimumDpAmount = {{ json_encode((float) $defaultSimulation['minimum_dp_amount']) }};
      var cashPrice = {{ json_encode((float) $cashPrice) }};
      var leasingRates = {!! json_encode(collect($leasingPartners)->mapWithKeys(fn ($partner) => [$partner['code'] => (float) $partner['annual_rate']])) !!};
      var currencyFormatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 2 });

      function syncTenorVisualState() {
        var index;
        var input;
        var label;

        for (index = 0; index < tenorInputs.length; index += 1) {
          input = tenorInputs[index];
          label = document.querySelector('label[for="' + input.id + '"]');

          if (!label) {
            continue;
          }

          if (input.checked) {
            label.classList.add('border-primary', 'bg-primary', 'text-white', 'shadow-lg', 'shadow-blue-900/10');
            label.classList.remove('border-slate-200', 'bg-white', 'text-primary', 'hover:border-primary/40');
          } else {
            label.classList.remove('border-primary', 'bg-primary', 'text-white', 'shadow-lg', 'shadow-blue-900/10');
            label.classList.add('border-slate-200', 'bg-white', 'text-primary', 'hover:border-primary/40');
          }
        }
      }

      function getSelectedTenor() {
        var index;

        for (index = 0; index < tenorInputs.length; index += 1) {
          if (tenorInputs[index].checked) {
            return Number(tenorInputs[index].value);
          }
        }

        return {{ (int) $defaultSimulation['tenor_months'] }};
      }

      function getSanitizedDpAmount(normalizeToMinimum) {
        var dpAmount = dpAmountField ? parseFloat(dpAmountField.value || '0') : 0;

        if (!isFinite(dpAmount) || dpAmount < 0) {
          dpAmount = 0;
        }
        if (dpAmount > cashPrice) {
          dpAmount = cashPrice;
        }
        if (normalizeToMinimum && dpAmount < minimumDpAmount) {
          dpAmount = minimumDpAmount;
        }

        return dpAmount;
      }

      function updateDpPreview(normalizeToMinimum) {
        var dpAmount;
        var dpPercentage;

        if (!dpAmountField || !dpPercentLabel) {
          return;
        }

        dpAmount = getSanitizedDpAmount(normalizeToMinimum);
        dpPercentage = cashPrice > 0 ? (dpAmount / cashPrice) * 100 : 0;

        if (normalizeToMinimum) {
          dpAmountField.value = String(Math.round(dpAmount));
        }

        dpPercentLabel.textContent = dpPercentage.toFixed(1).replace('.', ',') + '%';

        if (sidebarDpAmount) {
          sidebarDpAmount.textContent = currencyFormatter.format(dpAmount);
        }
        if (sidebarDpLabel) {
          sidebarDpLabel.textContent = dpAmount <= minimumDpAmount ? 'DP Minimum' : 'DP';
        }
        if (sidebarDpBadge) {
          if (dpAmount <= minimumDpAmount) {
            sidebarDpBadge.classList.remove('hidden');
            sidebarDpBadge.classList.add('inline-flex');
          } else {
            sidebarDpBadge.classList.add('hidden');
            sidebarDpBadge.classList.remove('inline-flex');
          }
        }

        if (dpFeedback) {
          if (dpAmount < minimumDpAmount) {
            dpFeedback.textContent = 'Inputkan Nominal DP sesuai budget anda. Minimun DP ' + currencyFormatter.format(minimumDpAmount) + ', 20 % dari harga unit.';
            dpFeedback.classList.remove('text-slate-500');
            dpFeedback.classList.add('text-amber-700');
          } else {
            dpFeedback.textContent = 'Inputkan Nominal DP sesuai budget anda. Minimun DP ' + currencyFormatter.format(minimumDpAmount) + ', 20 % dari harga unit.';
            dpFeedback.classList.remove('text-amber-700');
            dpFeedback.classList.add('text-slate-500');
          }
        }
      }

      function syncBookingFormFields(normalizeToMinimum) {
        var dpAmount = getSanitizedDpAmount(normalizeToMinimum);
        var dpPercentage = cashPrice > 0 ? (dpAmount / cashPrice) * 100 : 0;
        var tenor = getSelectedTenor();
        var annualRate = leasingField && leasingRates[leasingField.value] ? Number(leasingRates[leasingField.value]) : {{ json_encode((float) $defaultSimulation['annual_rate']) }};
        var financedAmount = Math.max(cashPrice - dpAmount, 0);
        var interestAmount = financedAmount * (annualRate / 100) * (tenor / 12);
        var monthlyInstallment = tenor > 0 ? ((financedAmount + interestAmount) / tenor) : 0;

        if (bookingLeasingField && leasingField) {
          bookingLeasingField.value = leasingField.value;
        }
        if (bookingDpPercentageField) {
          bookingDpPercentageField.value = dpPercentage.toFixed(2);
        }
        if (bookingDpAmountField) {
          bookingDpAmountField.value = dpAmount.toFixed(2);
        }
        if (bookingTenorField) {
          bookingTenorField.value = String(tenor);
        }
        if (bookingMonthlyInstallmentField) {
          bookingMonthlyInstallmentField.value = monthlyInstallment.toFixed(2);
        }
        if (bookingInterestRateField) {
          bookingInterestRateField.value = annualRate.toFixed(2);
        }
      }

      if (simulationForm && scrollPositionField) {
        simulationForm.addEventListener('submit', function () {
          updateDpPreview(true);
          syncBookingFormFields(true);
          scrollPositionField.value = String(window.pageYOffset || document.documentElement.scrollTop || 0);
        });
      }

      if (tenorInputs.length > 0) {
        syncTenorVisualState();

        for (var index = 0; index < tenorInputs.length; index += 1) {
          tenorInputs[index].addEventListener('change', function () {
            syncTenorVisualState();
            syncBookingFormFields(false);
          });
        }
      }

      if (leasingField) {
        leasingField.addEventListener('change', function () {
          syncBookingFormFields(false);
        });
      }

      if (dpAmountField) {
        updateDpPreview(false);
        syncBookingFormFields(false);

        dpAmountField.addEventListener('input', function () {
          updateDpPreview(false);
          syncBookingFormFields(false);
        });

        dpAmountField.addEventListener('blur', function () {
          updateDpPreview(true);
          syncBookingFormFields(true);
        });
      }

      if (bookingForm) {
        bookingForm.addEventListener('submit', function () {
          updateDpPreview(true);
          syncBookingFormFields(true);
        });
      }

      if (savedScrollPosition > 0) {
        window.addEventListener('load', function () {
          window.scrollTo(0, savedScrollPosition);
        });
      }
    })();
  </script>
</body>
</html>
