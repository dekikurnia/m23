require('./bootstrap');

import Swal from 'sweetalert2';

window.deleteConfirm = function(formId)
{
    Swal.fire({
        icon: 'warning',
        text: 'Anda yakin ingin menghapus data ini ?',
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonText: 'Hapus',
        confirmButtonColor: '#e3342f',
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}