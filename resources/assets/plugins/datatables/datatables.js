import jQuery from 'jquery';
import DataTable from 'datatables.net-dt';

window.jQuery = window.$ = jQuery;
window.DataTable = DataTable;

const defaults = {
    language: {
        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
        infoEmpty: 'Menampilkan 0 - 0 dari 0 data',
        infoFiltered: '',
        lengthMenu: '_MENU_',
        loadingRecords: '&nbsp;',
        search: '',
        searchPlaceholder: 'Cari…',
        processing: '<span class="dt-spinner"></span><span>Mohon tunggu…</span>',
        zeroRecords:
            '<div class="dt-empty-state">' +
            '<div class="dt-empty-icon"><i class="ri-search-2-line"></i></div>' +
            '<h5>Tidak ada hasil ditemukan</h5>' +
            '<p>Kami sudah mencari namun tidak menemukan data yang cocok dengan pencarian Anda.</p>' +
            '</div>',
        emptyTable:
            '<div class="dt-empty-state">' +
            '<div class="dt-empty-icon"><i class="ri-inbox-2-line"></i></div>' +
            '<h5>Belum ada data</h5>' +
            '<p>Data akan tampil di sini setelah ada aktivitas masuk.</p>' +
            '</div>',
        paginate: {
            next: '<i class="ri-arrow-right-s-line"></i>',
            previous: '<i class="ri-arrow-left-s-line"></i>',
            first: '<i class="ri-skip-left-line"></i>',
            last: '<i class="ri-skip-right-line"></i>',
        },
    },
    dom:
        "<'dt-toolbar'<'dt-toolbar-start'l><'dt-toolbar-end'f>>" +
        "<'dt-table-wrap'tr>" +
        "<'dt-footer'<'dt-footer-start'i><'dt-footer-end'p>>",
    pagingType: 'simple_numbers',
    lengthMenu: [10, 25, 50, 100],
    pageLength: 10,
    autoWidth: false,
};

jQuery.extend(true, DataTable.defaults, defaults);
