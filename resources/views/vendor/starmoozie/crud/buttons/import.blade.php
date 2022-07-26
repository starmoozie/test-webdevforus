<?php
    $route = $crud->route;
    $id    = explode('/', $route);
    $id    = $id[1];
?>
@if ($crud->hasAccess('import'))
    <input id="{{ $id }}" type="file" style="display: none;" accept=".xls, .xlsx, .csv"/>
    <a href="javascript:void(0)" onclick="importData(this)" data-route="{{ url($route.'/import') }}" class="btn btn-sm btn-success" data-button-type="importData">
        <span class="ladda-label"><i class="la la-file-excel"></i> {{ __('button.import') }}</span>
    </a>
@endif

<style>
    #overlay{	
    position: fixed;
    top: 0;
    z-index: 100;
    width: 100%;
    height:100%;
    display: none;
    background: rgba(0,0,0,0.6);
    }
    .cv-spinner {
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;  
    }
    .spinner {
    width: 40px;
    height: 40px;
    border: 4px #ddd solid;
    border-top: 4px #2e93e6 solid;
    border-radius: 50%;
    animation: sp-anime 0.8s infinite linear;
    }
    @keyframes sp-anime {
    100% { 
        transform: rotate(360deg); 
    }
    }
    .is-hide{
    display:none;
    }
</style>

@push('after_scripts') @if (request()->ajax()) @endpush @endif
    <div id="overlay">
        <div class="cv-spinner">
            <span class="spinner"></span>
        </div>
    </div>

    <script>
        if (typeof importData != 'function') {
            $("[data-button-type=importData]").unbind('click');

            function importData(button) {
                let id    = "{{ $id }}";
                let input = $(`#${id}`);

                input.trigger('click');
                input.change(function(e) {
                    let form_data = new FormData();
                    form_data.append('file', e.target.files[0]);

                    send(button, form_data)
                });
            }
        }

        function send(button, form_data) {
            $("#overlay").fadeIn(300);
            $.ajax({
                url: $(button).attr('data-route'),
                cache: false,
                type: 'POST',
                data: form_data,
                processData: false,
                contentType: false,
                enctype: 'multipart/form-data',
                success: function(result) {
                    new Noty({
                        text: "{{ __('alert.upload_success', ['attribute' => 'Mahasiswa']) }}",
                        type: "success"
                    }).show();

                    setTimeout(window.location.reload(), 5000);
                },
                error: function(result) {
                    // Show an alert with the result
                    new Noty({
                        text: "{{ __('alert.timeout', ['attribute' => 'Mahasiswa']) }}",
                        type: "warning"
                    }).show();

                    setTimeout(window.location.reload(), 8000);
                }
            });
        }
    </script>
@if (!request()->ajax()) @endpush @endif