@php
    // путь к логотипу: public/marga/images/whitelogoremovebg2.png
    $logoUrl = asset('marga/images/whitelogoremovebg2.png');
@endphp

<div class="flex items-center w-full">
    {{-- Лого + Admin --}}
    <a href="{{ url('/admin') }}" class="flex items-center gap-3">
        <div class="shrink-0 rounded-xl bg-white/5 p-1.5 ring-1 ring-white/10">
            <img
                src="{{ $logoUrl }}"
                alt="Marga Admin logo"
                class="h-11 w-11 object-contain"
            >
        </div>

        <span class="text-sm font-semibold tracking-[0.16em] uppercase text-slate-100" style="font-size: 20px;">
            Admin
        </span>
    </a>

    {{-- ✅ Кнопка "Перейти на сайт" --}}
  <a style="margin-left: 55px !important;" href="{{ url('/') }}" target="_blank"
   class="group ml-auto inline-flex items-center gap-2
          rounded-full px-4 py-2 text-xs font-semibold
          text-white whitespace-nowrap
          bg-gradient-to-r from-white/10 via-white/5 to-white/10
          ring-1 ring-white/15
          shadow-[0_8px_22px_rgba(0,0,0,0.18)]
          transition-all duration-200 ease-out
          hover:bg-gradient-to-r hover:from-[#66FCF1]/25 hover:via-white/10 hover:to-[#66FCF1]/15
          hover:ring-[#66FCF1]/40
          hover:shadow-[0_14px_40px_rgba(102,252,241,0.18)]
          hover:-translate-y-[1px]
          active:translate-y-0 active:scale-[0.98]">

    {{ __('ui.go_to_site') }}

    <svg class="h-4 w-4 opacity-90 transition-transform duration-200 group-hover:translate-x-0.5"
         xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 7h6m0 0v6m0-6L10 20l-3-3 10-10z"/>
    </svg>
</a>

</div>
