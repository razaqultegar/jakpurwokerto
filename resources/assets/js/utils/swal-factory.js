export function createSwal() {
    return window.Swal?.mixin({
        buttonsStyling: true,
        customClass: {
            popup: 'jpw-swal',
            container: 'jpw-swal-backdrop',
        },
    });
}

export function createToast() {
    return window.Swal?.mixin({
        toast: true,
        position: 'top-end',
        timer: 3500,
        showConfirmButton: false,
        timerProgressBar: true,
        customClass: {
            popup: 'jpw-toast',
            container: 'jpw-toast-container',
        },
    });
}
