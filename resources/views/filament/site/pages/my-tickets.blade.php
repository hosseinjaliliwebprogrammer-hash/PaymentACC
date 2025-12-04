<x-filament::page>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-100">My Support Tickets</h2>

        <a href="{{ url('/app/create-ticket') }}"
   class="inline-flex items-center px-4 py-2 rounded-lg text-white font-semibold shadow-md transition"
   style="background-color: rgb(245,158,11); box-shadow: 0 4px 10px rgba(245,158,11,0.3);"
   onmouseover="this.style.backgroundColor='rgb(251,191,36)'; this.style.boxShadow='0 6px 14px rgba(245,158,11,0.45)';"
   onmouseout="this.style.backgroundColor='rgb(245,158,11)'; this.style.boxShadow='0 4px 10px rgba(245,158,11,0.3)';">
    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
    New Ticket
</a>

    </div>

    {{ $this->table }}
</x-filament::page>
