 <div class="bg-white rounded-lg shadow-md p-4">
     <!-- header -->
     <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
         <x-input wire:model.live="search" type="text" class="w-full sm:w-1/2" placeholder="Buscar productos..." />

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
                        wire:click="order('title')">Nombre {!! $this->getSortIcon('title') !!}</th>
                     <th scope="col" class="min-w-24 cursor-pointer px-6 py-3"
                        wire:click="order('sku')">SKU {!! $this->getSortIcon('sku') !!}</th>
                     <th scope="col" class="min-w-24 cursor-pointer px-6 py-3"
                        wire:click="order('price')">Precio {!! $this->getSortIcon('price') !!}</th>
                     <th scope="col" class="px-6 py-3">Imagen</th>
                 </tr>
             </thead>

             <tbody>
                 @forelse ($products as $product)
                     <tr class="border-b border-gray-200 hover:bg-gray-50">
                         <td class="px-4 py-2">{{ $product['title'] }}</td>
                         <td class="px-4 py-2">{{ $product['variants'][0]['sku'] ?? 'N/A' }}</td>
                         <td class="px-4 py-2">{{ $product['variants'][0]['price'] }}</td>
                         <td class="px-4 py-2"><img src="{{ $product['images']['src'] ?? '' }}" width="50"></td>
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
