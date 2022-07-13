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
@endsection

@section('content')
    <div>
        <h1>게시판</h1>
    </div>
@endsection
