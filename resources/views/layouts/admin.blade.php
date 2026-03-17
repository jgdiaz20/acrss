<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://use.fontawesome.com" crossorigin>
    <link rel="preconnect" href="https://cdn.datatables.net" crossorigin>
    <link rel="preconnect" href="https://stackpath.bootstrapcdn.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com" crossorigin>

   
    <style>
        .modal{display:none;position:fixed;z-index:1050;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:rgba(0,0,0,0.5)}
        .modal-dialog{position:relative;width:auto;margin:10px}
        .modal-content{position:relative;background-color:#fff;border:1px solid rgba(0,0,0,.2);border-radius:.3rem;box-shadow:0 .25rem .5rem rgba(0,0,0,.5)}
        .modal-header{padding:1rem;border-bottom:1px solid #dee2e6}
        .modal-body{position:relative;flex:1 1 auto;padding:1rem}
        .modal-footer{display:flex;align-items:center;justify-content:flex-end;padding:1rem;border-top:1px solid #dee2e6}
        .qr-code-container{position:relative;display:inline-block}
        .spinner-border{display:inline-block;width:2rem;height:2rem;vertical-align:text-bottom;border:.25em solid currentColor;border-right-color:transparent;border-radius:50%;animation:spinner-border .75s linear infinite}
        @keyframes spinner-border{to{transform:rotate(360deg)}}
        .text-primary{color:#007bff!important}.text-success{color:#28a745!important}.text-muted{color:#6c757d!important}.bg-primary{background-color:#007bff!important}.bg-success{background-color:#28a745!important}.bg-light{background-color:#f8f9fa!important}.text-white{color:#fff!important}
    </style>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/acrss-logo-new.svg') }}">
    <link rel="shortcut icon" href="{{ asset('images/acrss-logo-new.svg') }}">
    <title>{{ trans('panel.site_title') }}</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/@coreui/coreui@2.1.16/dist/css/coreui.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    @yield('styles')
</head>
<body class="app header-fixed sidebar-fixed aside-menu-fixed pace-done sidebar-lg-show">
    <!-- Preloader -->
<div id="preloader" class="preloader">
    <div class="preloader-content">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <div class="mt-3 text-muted">Loading application...</div>
    </div>
</div>
    <header class="app-header navbar">
        <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="#">
            <span class="navbar-brand-full d-flex align-items-center">
                <img src="{{ asset('images/acrss-logo-new.svg') }}" 
                    alt="{{ trans('panel.site_title') }}" 
                    style="height: 35px; width: auto; margin-right: 10px;">
                {{ trans('panel.site_title') }}
            </span>
            <span class="navbar-brand-minimized d-flex align-items-center">
                <img src="{{ asset('images/acrss-logo-new.svg') }}" 
                    alt="{{ trans('panel.site_title') }}" 
                    style="height: 25px; width: auto;">
                <span class="d-none d-md-inline ml-2">{{ trans('panel.site_title') }}</span>
            </span>
        </a>

        <ul class="nav navbar-nav ml-auto">
            @if(count(config('panel.available_languages', [])) > 1)
                <li class="nav-item dropdown d-md-down-none">
                    <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        {{ strtoupper(app()->getLocale()) }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach(config('panel.available_languages') as $langLocale => $langName)
                            <a class="dropdown-item" href="{{ url()->current() }}?change_language={{ $langLocale }}">{{ strtoupper($langLocale) }} ({{ $langName }})</a>
                        @endforeach
                    </div>
                </li>
            @endif
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user"></i> {{ auth()->user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </li>

        </ul>
    </header>
<div id="app-content">
    <div class="app-body">
        @include('partials.menu')
        <main class="main">


            <div style="padding-top: 20px" class="container-fluid">
                @if(session('success') || session('status') || session('message'))
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ session('success') ?? session('status') ?? session('message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
                @if(session('error') || session('warning'))
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="alert alert-{{ session('error') ? 'danger' : 'warning' }} alert-dismissible fade show" role="alert">
                                <i class="fas fa-{{ session('error') ? 'exclamation-circle' : 'exclamation-triangle' }} mr-2"></i>
                                {{ session('error') ?? session('warning') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
                @if($errors->count() > 0)
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                            <div>
                                <strong><i class="fas fa-info-circle"></i> Validation Error{{ $errors->count() > 1 ? 's' : '' }}</strong>
                                <p class="mb-0">Please review the form below and correct {{ $errors->count() }} error{{ $errors->count() > 1 ? 's' : '' }} highlighted in red.</p>
                            </div>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @yield('content')

            </div>


        </main>
        <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/@coreui/coreui@2.1.16/dist/js/coreui.min.js"></script>
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    
        <script src="//cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/16.0.0/classic/ckeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        $(function() {
  let copyButtonTrans = '{{ trans('global.datatables.copy') }}'
  let csvButtonTrans = '{{ trans('global.datatables.csv') }}'
  let excelButtonTrans = '{{ trans('global.datatables.excel') }}'
  let pdfButtonTrans = '{{ trans('global.datatables.pdf') }}'
  let printButtonTrans = '{{ trans('global.datatables.print') }}'
  let colvisButtonTrans = '{{ trans('global.datatables.colvis') }}'
  let selectAllButtonTrans = '{{ trans('global.select_all') }}'
  let selectNoneButtonTrans = '{{ trans('global.deselect_all') }}'

  let languages = {
    'en': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/English.json'
  };

  $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, { className: 'btn' })
  $.extend(true, $.fn.dataTable.defaults, {
    language: {
      url: languages['{{ app()->getLocale() }}']
    },
    columnDefs: [{
        orderable: false,
        className: 'select-checkbox',
        targets: 0
    }, {
        orderable: false,
        searchable: false,
        targets: -1
    }],
    select: {
      style:    'multi+shift',
      selector: 'td:first-child'
    },
    order: [],
    scrollX: true,
    pageLength: 100,
    dom: 'lBfrtip<"actions">',
    buttons: [
      {
        extend: 'selectAll',
        className: 'btn-primary',
        text: selectAllButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'selectNone',
        className: 'btn-primary',
        text: selectNoneButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'copy',
        className: 'btn-default',
        text: copyButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'csv',
        className: 'btn-default',
        text: csvButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'excel',
        className: 'btn-default',
        text: excelButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'pdf',
        className: 'btn-default',
        text: pdfButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'print',
        className: 'btn-default',
        text: printButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      },
    ]
  });

  $.fn.dataTable.ext.classes.sPageButton = '';
});

    </script>
    <script>
(function() {
    // Preloader control script
    document.addEventListener('DOMContentLoaded', function() {
        const preloader = document.getElementById('preloader');
        const appContent = document.getElementById('app-content');
        
        // Show content when page is fully loaded
        window.addEventListener('load', function() {
            preloader.classList.add('fade-out');
            appContent.classList.add('visible');
            
            // Remove preloader from DOM after transition
            setTimeout(function() {
                preloader.classList.add('hidden');
            }, 300);
        });
        
        // Fallback: show content after 5 seconds max (prevents infinite loading)
        setTimeout(function() {
            if (!preloader.classList.contains('hidden')) {
                preloader.classList.add('fade-out', 'hidden');
                appContent.classList.add('visible');
            }
        }, 5000);
    });
})();
</script>
    @yield('scripts')
</body>

</html>
