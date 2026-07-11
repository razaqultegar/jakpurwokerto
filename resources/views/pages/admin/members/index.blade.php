@extends('layouts.admin')

@section('heading', $heading ?? $title)

@push('styles')
    @vite([
        'resources/assets/plugins/datatables/datatables.css',
    ])
@endpush

@push('scripts')
    @vite([
        'resources/assets/plugins/datatables/datatables.js',
        'resources/assets/js/pages/admin-members.js',
    ])
@endpush

@section('content')
    <div class="space-y-6">
        <div class="overflow-hidden rounded-xl border border-mercury bg-white shadow-sm"
            data-members-root
            data-data-url="{{ route('admin.members.data') }}"
            data-import-url="{{ route('admin.members.import') }}"
            data-template-url="{{ route('admin.members.template') }}">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-mercury px-5 py-4">
                <div>
                    <h3 class="text-sm font-semibold text-foreground">Daftar Anggota</h3>
                    <p class="text-xs text-onyx">Data anggota Biro 01 Purwokerto.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white transition hover:bg-primary-dark"
                        data-import-open>
                        <i class="ri-upload-2-line"></i>
                        Impor Excel
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table id="members-table" class="display w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">No KTA</th>
                            <th class="whitespace-nowrap">Nama</th>
                            <th class="whitespace-nowrap text-center">L/P</th>
                            <th class="whitespace-nowrap">Tgl Lahir</th>
                            <th class="whitespace-nowrap text-center">Usia</th>
                            <th class="whitespace-nowrap">Alamat</th>
                            <th class="whitespace-nowrap">Berlaku</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Impor Excel --}}
    <div id="import-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" data-import-modal>
        <div class="flex max-h-[90vh] w-full max-w-lg flex-col overflow-hidden rounded-2xl bg-white shadow-xl">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-3 px-6 py-5">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                        <i class="ri-upload-cloud-2-line text-lg"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-semibold text-foreground">Impor Data Anggota</h3>
                        <p class="mt-0.5 text-xs text-onyx">Unggah file Excel untuk menambahkan banyak anggota sekaligus.</p>
                    </div>
                </div>
                <button type="button" class="-mr-1 -mt-1 rounded-lg p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600" data-import-close>
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>

            {{-- Body --}}
            <form data-import-form class="flex-1 space-y-4 overflow-y-auto px-6 py-5">
                @csrf

                {{-- Dropzone --}}
                <label
                    class="group flex cursor-pointer flex-col items-center justify-center gap-2.5 rounded-xl border-2 border-dashed border-mercury bg-gray-50/80 px-6 py-9 text-center transition hover:border-primary hover:bg-primary-soft/20"
                    data-import-dropzone>
                    <span class="flex h-11 w-11 items-center justify-center rounded-full bg-primary-soft text-primary transition group-hover:scale-110">
                        <i class="ri-file-upload-line text-xl"></i>
                    </span>
                    <span class="text-xs font-medium text-foreground">
                        Tarik &amp; lepas file di sini, atau
                        <span class="text-primary hover:underline">pilih file</span>
                    </span>
                    <span class="max-w-full truncate text-[11px] text-onyx" data-import-filename>Belum ada file dipilih</span>
                    <input type="file" name="file" accept=".xlsx,.xls" class="hidden" data-import-input required>
                </label>

                {{-- Info & Unduh Sample --}}
                <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] text-onyx">
                    <span class="inline-flex items-center gap-1">
                        <i class="ri-information-line"></i>
                        Format <span class="font-medium text-foreground">.xlsx</span> · maks. 10MB
                    </span>
                    <a href="{{ route('admin.members.template') }}"
                        class="inline-flex items-center gap-1 font-medium text-primary transition hover:text-primary-dark hover:underline"
                        download>
                        <i class="ri-download-2-line"></i>
                        Unduh Sample
                    </a>
                </div>

                {{-- Progress --}}
                <div class="hidden rounded-xl border border-mercury bg-gray-50/60 p-3" data-import-progress-wrap>
                    <div class="mb-1.5 flex items-center justify-between text-xs">
                        <span class="font-medium text-foreground" data-import-status>Memproses…</span>
                        <span class="text-onyx" data-import-percent>0%</span>
                    </div>
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                        <div class="h-full w-0 rounded-full bg-primary transition-all duration-200" data-import-bar></div>
                    </div>
                </div>

                {{-- Result --}}
                <div class="hidden rounded-xl border border-mercury bg-gray-50/60 p-3 text-xs text-foreground" data-import-result></div>
            </form>

            {{-- Footer --}}
            <div class="flex items-center justify-between gap-2 border-t border-mercury bg-gray-50/50 px-6 py-4">
                <span class="text-[11px] text-onyx">Pastikan data sesuai sebelum mengimpor.</span>
                <div class="flex items-center gap-2">
                    <button type="button"
                        class="rounded-lg border border-mercury bg-white px-4 py-2 text-xs font-medium text-foreground transition hover:bg-gray-50"
                        data-import-close>Batal</button>
                    <button type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white transition hover:bg-primary-dark disabled:cursor-not-allowed disabled:opacity-50"
                        data-import-submit disabled>
                        <i class="ri-upload-2-line"></i>
                        Mulai Impor
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
