@extends('layout/layout')
@section('script')
    <script type="text/javascript">
        $(function() {
            $('#fullpage').fullpage({

                navigation: true,
                scrollBar: true,
                slidesNavigation: true,
                keyboardScrolling: true,

            });

        });
    </script>

    <style>
        header {
            position: fixed;
            z-index: 100;
        }
    </style>
@endsection

@section('content')
@endsection
