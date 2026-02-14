<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 uppercase italic tracking-tighter">
            👥 Gestion des Droits d'Accès
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="p-4 text-[10px] font-black uppercase text-gray-400">Utilisateur</th>
                            <th class="p-4 text-[10px] font-black uppercase text-gray-400">Email</th>
                            <th class="p-4 text-[10px] font-black uppercase text-gray-400 text-center">Rôle Actuel</th>
                            <th class="p-4 text-[10px] font-black uppercase text-gray-400 text-right">Actions de Promotion</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <span class="font-bold text-gray-800">{{ $user->name }}</span>
                            </td>
                            <td class="p-4 text-sm text-gray-500 font-mono">{{ $user->email }}</td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase border 
                                    {{ $user->role === 'admin' ? 'bg-red-50 text-red-700 border-red-100' : 
                                       ($user->role === 'manager' ? 'bg-indigo-50 text-indigo-700 border-indigo-100' : 'bg-gray-100 text-gray-600 border-gray-200') }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <form action="{{ route('users.updateRole', $user) }}" method="POST" class="inline-flex gap-2">
                                    @csrf @method('PATCH')
                                    <select name="role" class="text-[10px] font-bold uppercase rounded-lg border-gray-200 focus:ring-black">
                                        <option value="observer" {{ $user->role === 'observer' ? 'selected' : '' }}>Obs</option>
                                        <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <button type="submit" class="bg-black text-white px-3 py-1 rounded-lg text-[10px] font-black uppercase hover:bg-indigo-600 transition">
                                        OK
                                    </button>
                                </form>

                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline ml-4" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-400 hover:text-red-600 text-[10px] font-black uppercase">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>