<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Aplikasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Judul Halaman -->
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Kelola Data Aplikasi</h3>
                        <a href="{{ route('masterapps.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Tambah Aplikasi') }}
                        </a>
                    </div>

                    <!-- Form Pencarian -->
                    <div class="mb-6">
                        <form action="{{ route('masterapps.index') }}" method="GET" class="flex space-x-4">
                            <div class="flex-1">
                                <input type="text"
                                       name="search"
                                       value="{{ request('search') }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       placeholder="Cari nama aplikasi atau Git AWS..." />
                            </div>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                {{ __('Cari') }}
                            </button>
                            @if(request('search'))
                                <a href="{{ route('masterapps.index') }}"
                                   class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                    {{ __('Reset') }}
                                </a>
                            @endif
                        </form>
                    </div>

                    <!-- Tabel Daftar Aplikasi -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Nama Aplikasi') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Git AWS') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Domain Prod') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Domain Dev') }}
                                    </th>
                                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Username Login') }}
                                    </th>
                                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Password Login') }}
                                    </th>
                                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('DB IP') }}
                                    </th>
                                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('DB Name') }}
                                    </th>
                                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('DB Username') }}
                                    </th>
                                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Db Password') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Aksi') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($apps as $app)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $app->nama_apps }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $app->gitaws }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $app->domain_url_prod }}">
                                            {{ $app->domain_url_prod ?: '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $app->domain_url_dev }}">
                                            {{ $app->domain_url_dev ?: '-' }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $app->username_login_dev }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $app->password_login_dev }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $app->db_IP_port_dev }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $app->db_name }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $app->db_username }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $app->db_password }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="{{ route('masterapps.edit', $app->id) }}"
                                               class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Edit') }}
                                            </a>
                                            <button type="button"
                                                    onclick="openDeleteModal({{ $app->id }}, '{{ addslashes($app->nama_apps) }}')"
                                                    class="text-red-600 hover:text-red-900">
                                                {{ __('Hapus') }}
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            {{ __('Tidak ada data aplikasi ditemukan.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Menampilkan {{ $apps->firstItem() ?? 0 }} - {{ $apps->lastItem() ?? 0 }} dari {{ $apps->total() ?? 0 }}
                        </div>
                        <div>
                            {{ $apps->withQueryString()->links() }}
                        </div>
                    </div>


                    <!-- Modal Konfirmasi Hapus -->
                    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDeleteModal()"></div>

                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg font-medium text-gray-900" id="modal-title">
                                            Konfirmasi Penghapusan
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">
                                                Apakah Anda yakin ingin menghapus aplikasi <span id="appName" class="font-semibold"></span>?
                                            </p>
                                            <p class="text-sm text-gray-500 mt-1">
                                                Tindakan ini tidak bisa dibatalkan.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                    <form id="deleteForm" method="get" action="">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full inline-flex justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:col-start-2 sm:text-sm">
                                            Hapus
                                        </button>
                                    </form>
                                    <button type="button"
                                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:col-start-1 sm:mt-0 sm:text-sm"
                                            onclick="closeDeleteModal()">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Script untuk Modal Hapus -->
    <script>
        function openDeleteModal(appId, appName) {
            document.getElementById('appName').textContent = appName;
            document.getElementById('deleteForm').action = `/apps/destroy/${appId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Tutup modal jika klik di luar
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
</x-app-layout>