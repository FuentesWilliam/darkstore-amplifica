 <div class="bg-white rounded-lg shadow-md p-4">
     <!-- header -->
     <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
         <x-input wire:model.live="search" type="text" class="w-full sm:w-1/2" placeholder="Buscar pedidos..." />

         <div class="w-full sm:w-auto">
            <x-button class="w-full sm:w-auto" wire:click="export">Exportar</x-button>
        </div>
     </div>

     <!-- Tabla -->
     <div class="overflow-x-auto w-full">
         <table class="min-w-[600px] w-full table-auto text-sm text-left text-gray-500">

             <thead class="bg-gray-100  uppercase text-xs">
                 <tr>
                     <th scope="col" class="min-w-24 cursor-pointer px-6 py-3"
                        wire:click="order('customer')">Cliente </th>
                     <th scope="col" class="min-w-24 px-6 py-3">Fecha </th>
                     <th scope="col" class="min-w-24 px-6 py-3">Productos Comprados</th>
                     <th scope="col" class="min-w-24 cursor-pointer px-6 py-3"
                        wire:click="order('financial_status')">Estados</th>
                 </tr>
             </thead>

             <tbody>
                @forelse($orders as $order)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $order['customer'] }}</td>
                        <td class="px-4 py-2">{{ $order['created_at'] }}</td>
                        <td class="px-4 py-2">{{ count($order['line_items']) }}</td>
                        <td class="px-4 py-2">{{ $order['financial_status'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-gray-500">
                            No se encontraron resultados.
                        </td>
                    </tr>
                @endforelse
             </tbody>
         </table>

        
     </div>
