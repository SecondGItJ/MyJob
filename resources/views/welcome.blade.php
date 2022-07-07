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
    <section id="fullpage">
        <div class='section' style="background-color: red">
            <h1>1</h1>
        </div>

        <div class='section' style="background-color: yellow">
            <h1>2</h1>
        </div>

        <div class='section' style="background-color: blue">
            <h1>3</h1>
        </div>

        <div class='section' style="background-color: green">
            <h1>4</h1>
        </div>
    </section>
@endsection
