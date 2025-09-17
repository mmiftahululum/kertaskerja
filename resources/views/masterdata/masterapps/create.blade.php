<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($data) ? 'Edit Master App' : 'Tambah Master App' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                {{-- Tampilkan pesan validasi umum (opsional) --}}
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
                        Mohon periksa kembali input Anda.
                    </div>
                @endif

                @if(isset($data))
                    <form method="POST" action="{{ route('masterapps.update', $data->id) }}">
                        @csrf
                        @method('PUT')
                @else
                    <form method="POST" action="{{ route('masterapps.store') }}">
                @endif
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nama App --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Aplikasi</label>
                            <input type="text" name="nama_apps" value="{{ old('nama_apps', $data->nama_apps ?? '') }}"
                                class="mt-1 block w-full shadow-sm border-gray-300 rounded-md" />
                            @error('nama_apps') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Git/AWS --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Git / AWS</label>
                            <input type="text" name="gitaws" value="{{ old('gitaws', $data->gitaws ?? '') }}"
                                class="mt-1 block w-full shadow-sm border-gray-300 rounded-md" />
                            @error('gitaws') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Domain Prod --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Domain / URL (Production)</label>
                            <input type="text" name="domain_url_prod" value="{{ old('domain_url_prod', $data->domain_url_prod ?? '') }}"
                                class="mt-1 block w-full shadow-sm border-gray-300 rounded-md" />
                            @error('domain_url_prod') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Domain Dev --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Domain / URL (Development)</label>
                            <input type="text" name="domain_url_dev" value="{{ old('domain_url_dev', $data->domain_url_dev ?? '') }}"
                                class="mt-1 block w-full shadow-sm border-gray-300 rounded-md" />
                            @error('domain_url_dev') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Username Login Dev --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Username Login (Dev)</label>
                            <input type="text" name="username_login_dev" value="{{ old('username_login_dev', $data->username_login_dev ?? '') }}"
                                class="mt-1 block w-full shadow-sm border-gray-300 rounded-md" />
                            @error('username_login_dev') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Password Login Dev --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password Login (Dev)</label>
                            <input type="text" name="password_login_dev" value="{{ old('username_login_dev', $data->password_login_dev ?? '') }}"
                                class="mt-1 block w-full shadow-sm border-gray-300 rounded-md" />
                            <small class="text-gray-500">Biarkan kosong jika tidak ingin mengubah (hanya untuk edit).</small>
                            @error('password_login_dev') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- DB IP:PORT Dev --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">DB IP:Port (Dev)</label>
                            <input type="text" name="db_IP_port_dev" value="{{ old('db_IP_port_dev', $data->db_IP_port_dev ?? '') }}"
                                class="mt-1 block w-full shadow-sm border-gray-300 rounded-md" />
                            @error('db_IP_port_dev') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- DB Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">DB Name</label>
                            <input type="text" name="db_name" value="{{ old('db_name', $data->db_name ?? '') }}"
                                class="mt-1 block w-full shadow-sm border-gray-300 rounded-md" />
                            @error('db_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- DB Username --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">DB Username</label>
                            <input type="text" name="db_username" value="{{ old('db_username', $data->db_username ?? '') }}"
                                class="mt-1 block w-full shadow-sm border-gray-300 rounded-md" />
                            @error('db_username') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- DB Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">DB Password</label>
                            <input type="text" name="db_password" value="{{ old('db_password', $data->db_password ?? '') }}"
                                class="mt-1 block w-full shadow-sm border-gray-300 rounded-md" />
                            <small class="text-gray-500">Biarkan kosong jika tidak ingin mengubah (hanya untuk edit).</small>
                            @error('db_password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                     <div class="flex items-center justify-end gap-3 mt-4">
                        <a href="{{ route('masterapps.index') }}" class="inline-block px-4 py-2 bg-gray-200 outline-gray-100 rounded outline text-gray-700">Kembali</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
                            {{ isset($data) ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
