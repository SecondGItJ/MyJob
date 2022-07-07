<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="utf-8">
    <title>MyPage</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/3.0.9/fullpage.min.css">

    <script src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/3.0.9/fullpage.min.js"></script>

    @yield('script')

</head>

<body>
    <header>
        <div class="header_cont">
            <p class="logo_1"><a href="/"><img src="images/logo_1.png" /></a></p>
            <ul>
                <li><a href="#">기업정보</a></li>
                <li><a href="#">사업분야</a></li>
                <li><a href="#">기술분야</a></li>
                <li><a href="#">고객분야</a></li>
            </ul>
        </div>

        <div class="line"></div>

        <nav id="menu">
            <div id="menu_t">
                <ul>
                    <li><a href="#">회사소개</a></li>
                    <li><a href="#">구성원소개</a></li>
                    <li><a href="#">연혁/인증</a></li>
                    <li><a href="#">주요 고객사</a></li>
                    <li><a href="#">포트폴리오</a></li>
                </ul>

                <ul>
                    <li><a href="#">Smart Factory</a></li>
                    <li><a href="#">출입관리</a></li>
                    <li><a href="#">출장관리</a></li>

                </ul>

                <ul>
                    <li><a href="#">웹 사이트</a></li>
                    <li><a href="#">모바일</a></li>
                    <li><a href="#">응용 프로그램</a></li>
                </ul>

                <ul>
                    <li><a href="#">공지사항</a></li>
                    <li><a href="#">견적문의</a></li>
                    <li><a href="#">오시는 길</a></li>
                </ul>

            </div>
        </nav>
    </header>
    @yield('content')
</body>

</html>
