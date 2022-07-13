@php
use App\Http\Controllers\Common\MenuController;
use App\Http\Controllers\Helper\SessionHelper as LoginData;
use App\Http\Controllers\Common\FuncController as FC;

//FC::IpWhiteList();
$lang = new App\Http\Controllers\Helper\LangHelper;

$path = Request::path();
$url = Request::url();
//$current_path = Request::getRequestUri(); // URL + QueryString
$current_path = request()->get("current_request_uri");
$current_path = substr($current_path, 0, strpos($current_path, "?") !== false ? strpos($current_path, "?") : strlen($current_path));

$menu_array = MenuController::GetMenu();

// 팝업이거나 서브메뉴인 경우 해당 메뉴의 상위 메뉴를 현재 메뉴처럼 처리하도록 한다.
$param = request()->all();
$param["popup_url"] = $current_path;
$popup_result = FC::select_DB("SYS/MenuQuery.Select_PopupAsParent", $param);

if(FC::Count($popup_result) > 0) {
    $current_path = $popup_result[0]->menu_url;
}

$current_menu_name = "";
$current_menu_array = Array();
$current_menu_str = "";
$current_menu_id = "";
$current_parent_menu_id = "";
$current_top_menu_id = "";

$top_menu_array = Array();
$top_menu_cnt = 0;
$if_found = false;
for($i = 0; $i < FC::Count($menu_array); $i++) {
    if($menu_array[$i]->menu_depth == 1) {
        if(!$if_found) {
            $current_top_menu_id = $menu_array[$i]->menu_id;
        }
        $top_menu_array[$top_menu_cnt] = $menu_array[$i];
        $top_menu_cnt++;
    }
    if($if_found) {
        continue;
    }
    if($current_path == $menu_array[$i]->menu_url && strlen($menu_array[$i]->menu_url) > 0) {
        $current_menu_name = $menu_array[$i]->menu_name;
        $current_menu_str = $menu_array[$i]->menu_name_str;
        $current_menu_id = $menu_array[$i]->menu_id;
        $current_parent_menu_id = $menu_array[$i]->parent_menu_id;
        $if_found = true;
    }
}
if($if_found == false) {
    // 현재 들어온 메뉴에 대한 정보가 없는 경우
    if(in_Array('FM',LoginData::GetSession('user_role_id_ary'))) {
        $current_menu_name = "현장 승인 내역";
        $current_top_menu_id = "158";
    }else{
        $current_menu_name = "Dash";
        $current_top_menu_id = "0";
    }

}

$g_UserRoleIdAry = LoginData::GetSession('user_role_id_ary');
@endphp
<!DOCTYPE html>
<html lang="ko">

<head>
<title>{{ env('APP_NAME', 'REMS') }}</title>
    <!-- HTML5 Shim and Respond.js IE10 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 10]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="keywords" content="PLMS" />
    <meta name="author" content="WNPSoft" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon icon -->
    <link rel="icon" href="{{url('assets/images/favicon.ico')}}" type="image/x-icon">
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
    <!-- waves.css -->
    <link rel="stylesheet" href="{{url('assets/pages/waves/css/waves.min.css')}}" type="text/css" media="all">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="{{url('common/css/bootstrap/bootstrap.min.css')}}">

    <!-- waves.css -->
    <link rel="stylesheet" href="{{url('assets/pages/waves/css/waves.min.css')}}" type="text/css" media="all">
    <!-- themify icon -->
    <link rel="stylesheet" type="text/css" href="{{url('common/css/icon/themify-icons/themify-icons.css')}}">
    <!-- font-awesome-n -->
    <link rel="stylesheet" type="text/css" href="{{url('common/css/font-awesome/font-awesome-5.13.0.font-face.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('common/css/font-awesome/font-awesome-5.13.0.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('common/css/font-awesome/font-awesome-5.13.0-v4.css')}}">

    <!-- scrollbar.css -->
    <link rel="stylesheet" type="text/css" href="{{url('common/css/jquery.custom-scrollbar/jquery.mCustomScrollbar.css')}}">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="{{url('common/css/framework/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('common/css/framework/style_kyungbuk.css')}}">
    <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="{{url('common/css/icon/icofont/css/icofont.css')}}">
    <!-- Notification.css -->
    <link rel="stylesheet" type="text/css" href="{{url('assets/pages/notification/notification.css')}}">
    <!-- Animate.css -->
    <link rel="stylesheet" type="text/css" href="{{url('common/css/animate.css/css/animate.css')}}">
    <!-- morris chart -->
    <link rel="stylesheet" type="text/css" href="{{url('common/css/morris.js/css/morris.css')}}">

    <link rel="stylesheet" type="text/css" href="{{url('common/css/framework/non-bootstrap.css')}}">

    <link rel="stylesheet" type="text/css" href="{{url('common/css/daterangepicker/daterangepicker-3.0.5.css')}}">

    <!-- css block ends -->
    @yield('styles')

    <!-- 내부 포함 소스로 변경 해야됨 -->

    {{-- <script type="text/javascript" src="{{url('assets/js/ag-grid/ag-grid-community.min.noStyle.js')}} "></script> --}}
    <script type="text/javascript" src="{{url('common/js/ag-grid/ag-grid-community.min.noStyle_new.js')}} "></script>

    <link rel="stylesheet" type="text/css" href="{{url('common/css/ag-grid/ag-grid.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('common/css/ag-grid/ag-theme-balham.css')}}">

    <link rel="stylesheet" type="text/css" href="{{url('common/css/jstree/jstreeStyle.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('common/css/framework/style_black.css')}}">

    <!-- fancytree -->
    <link rel="stylesheet" type="text/css" href="{{url('common/css/fancytree-2.23/skin-win7/ui.fancytree.min.css')}}" />

    <!-- fancybox -->
    <link rel="stylesheet" type="text/css" href="{{url('common/js/jquery-fancybox/jquery.fancybox.min.css')}}" />

    <!-- fancytree -->
    <link rel="stylesheet" type="text/css" href="{{url('common/css/dropzone/dropzone.css')}}">

    <!-- chart.css -->
    <link rel="stylesheet" href="{{url('common/js/chartjs/Chart.min.css')}}" charset="utf-8" />

    <!-- bootstrap input-group -->
    <link rel="stylesheet" href="{{url('common/css/framework/bootstrap-input-group.css')}}" type="text/css" />

    <link rel="stylesheet" href="{{url('common/css/dropzone/dropzone-custom.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{url('proj/css/projcommon.css')}}" type="text/css" />

    <link rel="stylesheet" href="{{url('common/css/datepicker/datepicker-1.9.0.css')}}" />

    <link rel="stylesheet" href="{{url('common/css/framework/ranged-css.css')}}" />


    <!-- 내부 포함 소스로 변경 해야됨 -->

    <!-- Script block starts -->
    <!-- Required Jquery -->
    <script type="text/javascript" src="{{url('common/js/jquery/jquery-1.12.4.min.js')}} "></script>
    <script type="text/javascript" src="{{url('common/js/jstree/jstree.min.js')}} "></script>
    <script type="text/javascript" src="{{url('common/js/jquery-ui/jquery-ui.min.js')}} "></script>
    <script type="text/javascript" src="{{url('common/js/popper.js/popper.min.js')}}"></script>
    <script type="text/javascript" src="{{url('common/js/bootstrap/js/bootstrap.4.0.0.min.js')}} "></script>
    <script type="text/javascript" src="{{url('common/js/jquery-tmpl-master/jquery.tmpl.js')}} "></script>
    <!-- waves js -->
    <script src="{{url('assets/pages/waves/js/waves.min.js')}}"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="{{url('common/js/jquery-slimscroll/jquery.slimscroll.js')}}"></script>

    <!-- slimscroll js -->
    <script src="{{url('common/js/jquery-mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js')}} "></script>
    <!-- ag-grid -->
    <script type="text/javascript" src="{{url('common/js/framework/ag-grid_custom_input.js')}}"></script>
    <!-- javascript language -->
    <script type="text/javascript" src="{{url('common/js/framework/language.js')}} "></script>
    <!-- common -->
    <script type="text/javascript" src="{{url('common/js/framework/common.js')}} "></script>
    <!-- framework -->
    <script type="text/javascript" src="{{url('common/js/framework/framework.js')}} "></script>
    <!-- using ag-grid helper -->
    <script type="text/javascript" src="{{url('common/js/framework/gridhelper.js')}} "></script>
    <!-- using fancytree helper -->
    <script type="text/javascript" src="{{url('common/js/framework/treehelper.js')}} "></script>
    <!-- moment (date object helper) -->
    <script type="text/javascript" src="{{url('common/js/moment.js/moment-2.18.1.min.js')}} "></script>
    <!-- date range picker -->
    <script type="text/javascript" src="{{url('common/js/daterangepicker/daterangepicker-3.0.5.min.js')}} "></script>
    <!-- IE10, IE11 formData -->
    <script type="text/javascript" src="{{url('common/js/formdata/formdata.min.js')}} "></script>

    <!-- menu js -->
    <script src="{{url('common/js/etcs/pcoded.min.js')}}"></script>
    <script src="{{url('common/js/vertical/vertical-layout.min.js')}} "></script>

    <script type="text/javascript" src="{{url('common/js/etcs/script.js')}} "></script>

    <!-- Accordion js -->
    <script type="text/javascript" src="{{url('assets/pages/accordion/accordion.js')}}"></script>

    <!-- notification js -->
    <script type="text/javascript" src="{{url('common/js/bootstrap-growl/bootstrap-growl.min.js')}}"></script>

    <!-- tinyMCE -->
    <script type="text/javascript" src="{{url('common/js/tinymce/tinymce.min.js')}}" charset="utf-8"></script>

    <!-- full calendar css -->
    <link rel="stylesheet" type="text/css" href="{{url('common/js/fullCalendar-4.4.0/packages/core/main.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('common/js/fullCalendar-4.4.0/packages/daygrid/main.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('common/js/fullCalendar-4.4.0/packages/list/main.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('common/js/fullCalendar-4.4.0/packages/timegrid/main.css')}}">

    <!-- full calendar js -->
    <script type="text/javascript" src="{{url('common/js/fullCalendar-4.4.0/packages/core/main.min.js')}}" charset="utf-8"></script>
    <script type="text/javascript" src="{{url('common/js/fullCalendar-4.4.0/packages/core/locales/ko.js')}}" charset="utf-8"></script>
    <script type="text/javascript" src="{{url('common/js/fullCalendar-4.4.0/packages/daygrid/main.min.js')}}" charset="utf-8"></script>
    <script type="text/javascript" src="{{url('common/js/fullCalendar-4.4.0/packages/interaction/main.min.js')}}" charset="utf-8"></script>
    <script type="text/javascript" src="{{url('common/js/fullCalendar-4.4.0/packages/list/main.min.js')}}" charset="utf-8"></script>
    <script type="text/javascript" src="{{url('common/js/fullCalendar-4.4.0/packages/timegrid/main.min.js')}}" charset="utf-8"></script>

    <!-- fancytree -->
    <script type="text/javascript" src="{{url('common/js/fancytree-2.23/jquery.fancytree-all-deps.min.js')}}"></script>

    <!-- fancybox -->
    <script type="text/javascript" src="{{url('common/js/jquery-fancybox/jquery.fancybox.min.js')}}"></script>

    <!-- dropzone -->
    <script type="text/javascript" src="{{url('/common/js/dropzone/dropzone.js')}}"></script>

    <!-- other js -->
    @if(!env('APP_DEBUG', false))
    <script type="text/javascript" src="{{url('common/js/framework/loop.js')}}"></script>
    @endif

    <!-- project js -->
    <script type="text/javascript" src="{{url('proj/js/projcommon.js')}} "></script>
    <script type="text/javascript" src="{{url('proj/js/projframework.js')}} "></script>
    <script type="text/javascript" src="{{url('proj/js/projgridhelper.js')}} "></script>

    <!-- daum postcode js -->
    <script type="text/javascript" src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>

    <!-- Chart.js -->
    <script type="text/javascript" src="{{url('common/js/chartjs/Chart.min.js')}}" charset="utf-8"></script>
    <script type="text/javascript" src="{{url('common/js/chartjs/Chart.bundle.min.js')}}" charset="utf-8"></script>

    <script type="text/javascript" src="{{url('common/js/framework/uploadhelper.js')}}" charset="utf-8"></script>
    <!-- imask -->
    <script type="text/javascript" src="{{url('common/js/imask/imask-6.0.5.js')}}" charset="utf-8"></script>

    <!-- datepicker -->
    <script type="text/javascript" src="{{url('common/js/datepicker/datepicker-1.9.0.js')}}"></script>
    <script type="text/javascript" src="{{url('common/js/datepicker/datepicker.ko.min.js')}}"></script>

    <!-- treePopup.js -->
    <script type="text/javascript" src="{{url('common/js/framework/treepopup.js')}}"></script>

    <!-- charthelper.js -->
    <script type="text/javascript" src="{{url('common/js/framework/charthelper.js')}}"></script>

    <!-- Language pack -->
    {!! $lang->SetJSLang(true) !!}

    <!-- custom css -->
    <style type="text/css">
    #noti_list {max-height:300px; overflow-y:auto;}
    </style>

    <!-- Custom js -->
    <script>
        // {{-- 서버 세팅 (최대 파일 크기) --}}
        var g_ServerMaxUploadFileSize = parseInt('{!! FC::GetUploadableSize() !!}', 10);
        // {{-- 업로드 가능한 최대 파일 개수 --}}
        var g_ServerMaxUploadFileCount = parseInt('{!! FC::GetUploadableFileCount() !!}', 10);
        // {{-- 세션값 --}}
        var g_UserCompCode = "{{ LoginData::GetSession('user_comp_code') }}";
        var g_UserId = "{{ LoginData::GetSession('user_id') }}";
        var g_UserName = "{{ LoginData::GetSession('user_name') }}";
        var g_UserToken = "{{ LoginData::GetSession('user_token') }}";
        var g_UserCompName = "{{ LoginData::GetSession('user_comp_name') }}";
        var g_UserDeptCode = "{{ LoginData::GetSession('user_dept_code') }}";
        var g_UserDeptName = "{{ LoginData::GetSession('user_dept_name') }}";
        var g_UserRoleId = "{{ LoginData::GetSession('user_role_id') }}";
        var g_UserRoleName = "{{ LoginData::GetSession('user_role_name') }}";
        var g_UserRoleIdAry = @JSON(LoginData::GetSession('user_role_id_ary'));
        var g_UserRoleNameAry = @JSON(LoginData::GetSession('user_role_name_ary'));
        var g_UserPosGubn = "{{ LoginData::GetSession('user_pos_gubn') }}";
        var g_UserPosGubnName = "{{ LoginData::GetSession('user_pos_gubn_name') }}";
        var g_UserLangPack = "{{ LoginData::GetSession('user_lang_pack') }}";
        var g_UserRoleGubnAry = @JSON(LoginData::GetSession('user_role_gubn_ary'));
        var g_UserRoleGubnNameAry = @JSON(LoginData::GetSession('user_role_gubn_name_ary'));

        var g_AlarmTimer = null;
        var g_ShowAppDocNo = "";
        var g_ChgGubn = "";

        function readAlarmProcess(p_Obj) {

            // 알람 읽음 처리 코드는 추후 사용 여지가 있어서 남김
            // 쿼리는 삭제함
            return;

            setLoadImage(false);
            g_ShowAppDocNo = p_Obj.attr("data-show_app_doc_no");
            g_ChgGubn = p_Obj.attr("data-chg_gubn");

            var frm = $("#frmLayoutAlarmMain");
            frm.addParam("query_id", "Common/ProjectQuery.Update_LayoutReadDateTime");
            frm.addParam("app_doc_no", p_Obj.attr("data-app_doc_no"));
            frm.addParam("app_doc_seq", p_Obj.attr("data-app_doc_seq"));
            frm.addParam("func", "IS_LAM");
            frm.addParam("afterAction", false);
            frm.request();
        }

        function handleIS_LAM(data, textStatus, jqXHR) {
            setLoadImage(true);
            if(g_ChgGubn == "C012") {
                location.href="{{url('/apr/apr_doc')}}?k_show_app_doc_no=" + g_ShowAppDocNo;
            } else {
                location.href="{{url('/apr/status')}}?k_show_app_doc_no=" + g_ShowAppDocNo;
            }
        }

        // 알람 버튼 클릭시 전부 읽음 처리
        function readAlarmProcessAll() {
            // 알람 읽음 처리 코드는 추후 사용 여지가 있어서 남김
            // 쿼리는 삭제함
            return;
            setLoadImage(false);
            var frm = $("#frmLayoutAlarmMain");
            frm.addParam("query_id", "Common/ProjectQuery.Update_LayoutReadDateTimeAll");
            frm.addParam("func", "IS_LAM2");
            frm.addParam("afterAction", false);
            frm.request();
        }

        function handleIS_LAM2(data, textStatus, jqXHR) {
            setLoadImage(true);
        }

        // function loadAlarmList() {
        //     setLoadImage(false);
        //     var frm = $("#frmLayoutAlarmMain");
        //     frm.addParam("query_id", "Common/ProjectQuery.Select_LayoutAlarmList");
        //     frm.addParam("func", "IQ_LAM");
        //     frm.request();
        // }

        // function handleIQ_LAM(data, textStatus, jqXHR) {
        //     setLoadImage(true);
        //     var log_html = "";
        //     var log_count = 0;
        //     var log_count_str = "";

        //     if(data.length > 0) {
        //         for(var i = 0; i < data.length; i++) {
        //             log_count += parseInt(data[i]["alarm_cnt"], 10);
        //             log_html += '<li class="waves-effect waves-light" data-show_app_doc_no="' + data[i]["show_app_doc_no"] + '" data-app_doc_no="' + data[i]["app_doc_no"] + '" data-app_doc_seq="' + data[i]["app_doc_seq"] + '" data-chg_gubn="' + data[i]["chg_gubn"] + '">';
        //             log_html += '    <div class="media-body">';
        //             log_html += '        <h5 class="notification-user">' + data[i]["show_app_doc_no"];
        //             if(parseInt(data[i]["alarm_cnt"], 10) > 0){
        //                 log_html += ' <span style="color:red;"><i class="fas fa-bolt"></i></span>';
        //             }
        //             log_html += '</h5>';
        //             log_html += '        <p class="notification-msg">' + data[i]["alarm_message"] + '</p>';
        //             log_html += '    </div>';
        //             log_html += '</li>';
        //         }

        //         $("#noti_list").html(log_html);
        //     } else {
        //         log_count = 0;

        //         $("#noti_list").html('<li class="waves-effect waves-light"><div class="media-body"><h5 class="notification-user">알림이 없습니다.</h5></div></li>');
        //     }

        //     if(log_count > 99) {
        //         log_count_str = "99+";
        //     } else {
        //         log_count_str = log_count + "";
        //     }

        //     if(log_count > 0) {
        //         $("#control_noti i span.badge").attr("style", "font-size: 12px; top: 0px; right: 0px; display: inherit !important;").text(log_count_str);
        //     } else {
        //         $("#control_noti i span.badge").attr("style", "font-size: 12px; top: 0px; right: 0px; display: none !important;").text("");
        //     }
        // }

        // function initAlarmInterval() {
        //     // 알람 타이머 시작 (10초)
        //     g_AlarmTimer = setInterval(loadAlarmList, 10000);
        // }

        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover({
                html: true,
                content: function() {
                    return $('#primary-popover-content').html();
                }
            });

            // menu control
            $("a[data-sec_depth]").click(function(ev) {
                ev.preventDefault();
                var obj = $(this);
                sec_depth = obj.attr("data-sec_depth");
                $("#sub_menu_wrapper [data-sec_depth]").css("display", "none");
                $("#sub_menu_wrapper [data-sec_depth='" + sec_depth + "']").css("display", "");
                $("#menu_wrapper div.menu-bottom").addClass("hide");
                $("a.top-menu-link").removeClass("selected");
                obj.addClass("selected");
                obj.next().removeClass("hide");

                $('.pcoded[theme-layout="vertical"][vertical-nav-type="expanded"] .pcoded-navbar').width(247);
            });

            // 기존 이벤트 제거
            $("#mobile-collapse").off("click");
            // 왼쪽 메뉴 크기 컨트롤
            $("#mobile-collapse").click(function(ev) {

                ev.preventDefault();

                if($(window).width() >= 993 - 16) {
                    // 큰 화면
                    if($('.pcoded[theme-layout="vertical"][vertical-nav-type="expanded"] .pcoded-navbar').width() <= 60) {
                        $('.pcoded[theme-layout="vertical"][vertical-nav-type="expanded"] .pcoded-navbar').width(247);
                        $("#body_content_wrapper").css({
                            "width" : "calc(100% - 247px)",
                            "margin-left" : 247
                        });
                    } else {
                        $('.pcoded[theme-layout="vertical"][vertical-nav-type="expanded"] .pcoded-navbar').width(0);
                        $("#body_content_wrapper").css({
                            "width" : "calc(100%)",
                            "margin-left" : 0
                        });
                    }
                } else {
                    // 작은 화면
                    $("#mobile_menu_wrapper").slideToggle(400);
                }
            });

            $(window).resize(function() {
                if($(window).width() >= 993) {
                    $('.pcoded[theme-layout="vertical"][vertical-nav-type="expanded"] .pcoded-navbar').width(247);
                    $("#body_content_wrapper").css({
                        "width" : "calc(100% - 247px)",
                        "margin-left" : 247
                    });
                } else {
                    $('.pcoded[theme-layout="vertical"][vertical-nav-type="expanded"] .pcoded-navbar').width(0);
                    $("#body_content_wrapper").css({
                        "width" : "calc(100%)",
                        "margin-left" : 0
                    });
                }
            });

            // 모바일 메뉴 컨트롤
            $("#mobile_menu_wrapper a.has-child").click(function(ev) {
                ev.preventDefault();
                var obj = $(this);

                if(obj.hasClass("closed")) {

                    $("#mobile_menu_wrapper a.has-child").each(function(idx, elem) {
                        if(obj.next().hasClass("level2-wrapper")) {
                            if($(elem).next().hasClass("level2-wrapper")) {
                                $(elem).addClass("closed").next().slideUp(400);
                            }
                        } else if(obj.next().hasClass("level3-wrapper")) {
                            if($(elem).next().hasClass("level3-wrapper")) {
                                $(elem).addClass("closed").next().slideUp(400);
                            }
                        }
                    });

                    obj.removeClass("closed").next().slideDown(400);
                } else {
                    obj.addClass("closed").next().slideUp(400);
                }
            });

            // page open menu selected
            $("#sub_menu_wrapper [data-sec_depth]").css("display", "none");
            if($("a[data-sec_depth].selected").length > 0) {
                $("#sub_menu_wrapper [data-sec_depth='" + $("a[data-sec_depth].selected").attr("data-sec_depth") + "']").css("display", "");
            } else {
                $("#sub_menu_wrapper [data-sec_depth='" + $("a[data-sec_depth]:eq(0)").attr("data-sec_depth") + "']").css("display", "");
            }

            // 종 컨트롤
            $("#control_noti").click(function(ev) {
                ev.preventDefault();
                $("#noti_list").slideToggle(500);
                $("#noti_list").parent().toggleClass("active");
            });

            $("#control_mymenu").find("a.waves-effect").click(function(ev) {
                ev.preventDefault();
                $(this).parent().find(".show-notification").slideToggle(500);
            });

            $("#control_mymenu2").click(function(ev) {
                ev.preventDefault();
                $(this).siblings("ul[name='fm_logout']").slideToggle(500);
            });

            $("ul[name='fm_logout']").hide();

            $('.pcoded[theme-layout="vertical"][vertical-nav-type="expanded"] .pcoded-navbar').width(247);
            $("#body_content_wrapper").css({
                "width" : "calc(100% - 247px)",
                "margin-left" : 247
            });

            // 알람 읽음 처리
            $("#layout_alarm").click(function(){
                if($("#layout_alarm").hasClass("active")) {
                    // clearInterval(g_AlarmTimer);
                    // readAlarmProcessAll();
                } else {
                    //initAlarmInterval();
                    //loadAlarmList();
                }
            });

            // 알림 클릭
            $(document).on("click", "li.waves-effect[data-show_app_doc_no]", function() {
                //location.href="{{url('/apr/status')}}?k_show_app_doc_no=" + $(this).attr("data-show_app_doc_no");
                var obj = $(this);
                readAlarmProcess(obj);
            });

            $(".alert-browser").find("a").click(function(ev) {
                ev.preventDefault();
                var obj = $(this);
                obj.parent().slideUp(200, function() {
                    obj.parent().css({
                        "display" : "none"
                    });

                    $(".pcoded-main-container").css("margin-top", 56);
                });

                $(".pcoded-main-container").animate({
                    "margin-top" : 56
                }, 200)
            });

            //loadAlarmList();
            //initAlarmInterval();

            $(window).trigger("resize");

            setDataHeight();
        });
    </script>

    <!-- Morris Chart js -->
    {{-- <script src="{{url('assets/js/raphael/raphael.min.js')}}"></script>
    <script src="{{url('assets/js/morris.js/morris.js')}}"></script> --}}
    <!-- Custom js -->
    {{-- <script src="{{url('assets/pages/chart/morris/morris-custom-chart.js')}}"></script> --}}

    <!-- Google map js -->
    {{-- <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script> --}}
    {{-- <script type="text/javascript" src="{{url('assets/pages/google-maps/gmaps.js')}}"></script> --}}
    <!-- Custom js -->
    {{-- <script type="text/javascript" src="{{url('assets/pages/google-maps/google-maps.js')}}"></script> --}}

    <!-- Script block ends -->
    @yield('script')
</head>

<body>
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="loader-track">
            <div class="preloader-wrapper">
                <div class="spinner-layer spinner-blue">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>

                <div class="spinner-layer spinner-yellow">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>

                <div class="spinner-layer spinner-green">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
            <nav id="menu_all_wrapper" class="navbar header-navbar pcoded-header">
                @if(FC::getBrowser() == "4")
                <div class="alert-browser"><img src="{{url('images/alert_image.png')}}" style="width:15px; height:15px; vertical-align:sub" /> 현재 시스템은 크롬 브라우저에 최적화 되어있습니다. 크롬 브라우저를 사용해주시기 바랍니다. <a href="#"><i class="fa fa-times">&nbsp;</i></a></div>
                @endif
                <div class="navbar-wrapper">
                    <div class="navbar-logo">
                        @if($menu_array[1]->menu_id != 158)
                            <a class="mobile-menu waves-effect waves-light" id="mobile-collapse" href="#!">
                                <i class="ti-menu"></i>
                            </a>
                        @endif
                        <a href="{{ url('home/index') }}">
                            <img class="img-fluid" src="{{url('assets/images/logo3.png')}}" alt="Theme-Logo" style="height:52px;" />
                        </a>
                        @if($menu_array[1]->menu_id != 158)
                            <a class="mobile-options waves-effect waves-light">
                                <i class="ti-more"></i>
                            </a>
                        @else
                            <a href="#!" class="waves-effect waves-light" id = "control_mymenu2" style="display: block;position: absolute;right: 0;top: 0;font-size: 14px;line-height: 4.0;width: 90px;">
                                <span>{{LoginData::GetSession("user_name")}}</span>
                            </a>
                            <ul class="show-notification" name = "fm_logout" style="background-color: rgb(255, 255, 255);border-radius: 5px;box-shadow: rgba(0, 0, 0, 0.25) 0px 0px 35px 0px;display: block;width: 7em;padding: 15px 0px;position: absolute;right: 0;">
                                <li class="waves-effect waves-light">
                                    <a href="{{ url('logout') }}">
                                        <i class="ti-layout-sidebar-left"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        @endif
                    </div>
                    <div class="navbar-container container-fluid">
                        {{-- 모바일 메뉴 --}}
                        <ul class="nav-left" id="mobile_menu_wrapper" style="display:none;">
                            @for($i = 0; $i < FC::Count($menu_array); $i++)
                                @if($menu_array[$i]->menu_depth == "1")
                                    @if($i != 0)
                                        @if ($menu_array[$i-1]->menu_depth != "1")
                                            </ul>
                                        @endif
                                        </li>
                                    @endif
                            <li class="level1">
                                <a href="{{$menu_array[$i]->child_node == "Y" && $menu_array[$i]->menu_url == "" ? "javascript:void(0)" : url($menu_array[$i]->menu_url)}}" class="{{(strlen($menu_array[$i]->menu_url) > 0 && $menu_array[$i]->menu_url == $current_path) || $current_top_menu_id == $menu_array[$i]->menu_id ? "active":"" }}{{$menu_array[$i]->child_node == "Y" ? " has-child closed" : ""}}"><span>&nbsp;</span>{{ $menu_array[$i]->menu_name }}</a>
                                    @if($menu_array[$i]->child_node == "Y")
                                <ul class="level2-wrapper" style="display:none;">
                                    @else
                            </li>
                                    @endif
                                @elseif($menu_array[$i]->menu_depth == "2")
                                        @if ($menu_array[$i-1]->menu_depth == "3")
                                            </li>
                                        @endif
                                    <li class="level2">
                                        <a href="{{$menu_array[$i]->child_node == "Y" && $menu_array[$i]->menu_url == "" ? "javascript:void(0)" : url($menu_array[$i]->menu_url)}}" class="{{(strlen($menu_array[$i]->menu_url) > 0 && $menu_array[$i]->menu_url == $current_path) ? "active":"" }}{{$menu_array[$i]->child_node == "Y" ? " has-child closed" : ""}}"><span>&nbsp;</span>{{ $menu_array[$i]->menu_name }}</a>
                                    @if($menu_array[$i]->child_node == "Y")
                                        <ul class="level3-wrapper" style="display:none;">
                                    @else
                                    </li>
                                    @endif
                                @elseif($menu_array[$i]->menu_depth == "3")
                                            <li class="level3">
                                                <a href="{{$menu_array[$i]->child_node == "Y" && $menu_array[$i]->menu_url == "" ? "javascript:void(0)" : url($menu_array[$i]->menu_url)}}" class="{{(strlen($menu_array[$i]->menu_url) > 0 && $menu_array[$i]->menu_url == $current_path) ? "active":"" }}{{$menu_array[$i]->child_node == "Y" ? " has-child closed" : ""}}"><span>&nbsp;</span>{{ $menu_array[$i]->menu_name }}</a>
                                            </li>
                                    @if($menu_array[$i]->mn_desc == "1" && $menu_array[$i]->child_node != "Y")
                                        </ul>
                                    @endif
                                @endif
                            @endfor
                            @if ($menu_array[FC::Count($menu_array) - 1]->menu_depth == 3)
                                    </li></ul>
                            @endif
                            @if ($menu_array[FC::Count($menu_array) - 1]->menu_depth == 2)
                                    </ul>
                            @endif
                            </li>
                        </ul>
                        <ul class="nav-left" id="menu_wrapper">
                            <li>
                                <div class="sidebar_toggle"><a href="javascript:void(0)"><i class="ti-menu"></i></a></div>
                            </li>
                            @if(FC::getBrowser() != "4")
                            <li>
                                <a href="#!" onclick="javascript:toggleFullScreen()" class="waves-effect waves-light">
                                    <i class="ti-fullscreen"></i>
                                </a>
                            </li>
                            @endif
                            <li class="top-menu-seperator"></li>

                            @for($i = 0; $i < FC::Count($top_menu_array); $i++)
                            <li class="top-menu">
                                <a class="top-menu-link{{$current_top_menu_id == $top_menu_array[$i]->menu_id ? " selected" : ""}}" data-sec_depth="{{$top_menu_array[$i]->menu_id}}" href="{{$top_menu_array[$i]->menu_url == '' ? url("/") : url($top_menu_array[$i]->menu_url)}}"><span>{{$top_menu_array[$i]->menu_name}}</span></a>
                                <div class="menu-bottom{{$current_top_menu_id == $top_menu_array[$i]->menu_id ? "" : " hide"}}"><img src="{{url('images/menu_bottom_arrow.png')}}"></div>
                            </li>
                                @if($i < FC::Count($top_menu_array) - 1)
                            <li class="top-menu-seperator-line"></li>
                                @endif
                            @endfor

                            {{-- <!-- top-menu start -->
                            <li class="top-menu">
                                <a class="top-menu-link" href="#!"><span>전자결재</span></a>
                            </li>
                            <li class="top-menu-seperator-line"></li>
                            <li class="top-menu">
                                <a class="top-menu-link selected" href="#!"><span>시스템 관리</span></a>
                            </li>
                            <li class="top-menu-seperator-line"></li>
                            <li class="top-menu">
                                <a class="top-menu-link" href="#!"><span>테스트</span></a>
                            </li>
                            <li class="top-menu-seperator-line"></li>
                            <li class="top-menu">
                                <a class="top-menu-link" href="#!"><span>메뉴 테스트요</span></a>
                            </li>
                            <li class="top-menu-seperator-line"></li>
                            <li class="top-menu">
                                <a class="top-menu-link selected" href="#!"><span>메뉴 테스트요</span></a>
                            </li>
                            <!-- top-menu finish --> --}}
                        </ul>
                        <ul class="nav-right">
                            <form id="frmLayoutAlarmMain" method="POST"></form>
                            {{--
                            <li class="header-notification" id="layout_alarm">
                                <a href="#!" id="control_noti" class="waves-effect waves-light" style="line-height:50px; padding-top: 6px;">
                                    <i class="ti-bell" style="font-size:20px;">
                                        <span class="badge bg-danger" style="font-size:12px; top:0px; right:0px; display:none !important;">0</span>
                                    </i>
                                </a>
                                <ul class="show-notification" id="noti_list">
                                </ul>
                                    <li>
                                        <h6>Notifications</h6>
                                        <label class="label label-danger">New</label>
                                    </li>
                                    <li class="waves-effect waves-light">
                                        <div class="media">
                                            <img class="d-flex align-self-center img-radius" src="{{url('assets/images/avatar-2.jpg')}}" alt="Generic placeholder image">
                                            <div class="media-body">
                                                <h5 class="notification-user">John Doe</h5>
                                                <p class="notification-msg">Lorem ipsum dolor sit amet, consectetuer elit.</p>
                                                <span class="notification-time">30 minutes ago</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="waves-effect waves-light">
                                        <div class="media">
                                            <img class="d-flex align-self-center img-radius" src="{{url('assets/images/avatar-4.jpg')}}" alt="Generic placeholder image">
                                            <div class="media-body">
                                                <h5 class="notification-user">Joseph William</h5>
                                                <p class="notification-msg">Lorem ipsum dolor sit amet, consectetuer elit.</p>
                                                <span class="notification-time">30 minutes ago</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="waves-effect waves-light">
                                        <div class="media">
                                            <img class="d-flex align-self-center img-radius" src="{{url('assets/images/avatar-3.jpg')}}" alt="Generic placeholder image">
                                            <div class="media-body">
                                                <h5 class="notification-user">Sara Soudein</h5>
                                                <p class="notification-msg">Lorem ipsum dolor sit amet, consectetuer elit.</p>
                                                <span class="notification-time">30 minutes ago</span>
                                            </div>
                                        </div>
                                    </li>

                                </ul>
                            </li> --}}
                            <li class="user-profile header-notification" id="control_mymenu">
                                <a href="#!" class="waves-effect waves-light">
                                    {{-- <img src="{{url('assets/images/avatar-4.jpg')}}" class="img-radius" alt="User-Profile-Image"> --}}
                                    <span>{{LoginData::GetSession("user_name")}}</span>
                                    <i class="ti-angle-down"></i>
                                </a>
                                <ul class="show-notification profile-notification">
                                    {{-- <li class="waves-effect waves-light">
                                        <a href="#!">
                                            <i class="ti-settings"></i> Settings
                                        </a>
                                    </li>
                                    <li class="waves-effect waves-light">
                                        <a href="{{ url('sample/user-profile') }}">
                                            <i class="ti-user"></i> Profile
                                        </a>
                                    </li>
                                    <li class="waves-effect waves-light">
                                        <a href="{{ url('sample/email-inbox') }}">
                                            <i class="ti-email"></i> My Messages
                                        </a>
                                    </li>
                                    <li class="waves-effect waves-light">
                                        <a href="{{ url('sample/auth-lock-screen') }}">
                                            <i class="ti-lock"></i> Lock Screen
                                        </a>
                                    </li> --}}
                                    @if(in_array("AD",$g_UserRoleIdAry) && LoginData::GetSession('user_comp_code') == "wnpsoft")
                                        <li class="waves-effect waves-light">
                                            <a href="{{ url('/home/log_status') }}">
                                                <i class="fa fa-file-text-o"></i> log 현황
                                            </a>
                                        </li>
                                        <li class="waves-effect waves-light">
                                            <a href="{{ url('/home/plc_status') }}">
                                                <i class="fa fa-align-justify"></i> plc 현황
                                            </a>
                                        </li>
                                    @endif
                                    <li class="waves-effect waves-light">
                                        <a href="{{ url('/home/user_info') }}">
                                            <i class="ti-user"></i> 정보수정
                                        </a>
                                    </li>
                                    <li class="waves-effect waves-light">
                                        <a href="{{ url('logout') }}">
                                            <i class="ti-layout-sidebar-left"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    <nav class="pcoded-navbar" style="width:0px;">
                        <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
                        <div class="pcoded-inner-navbar main-menu" id="sub_menu_wrapper">
                            <div class="">
                                <div class="main-menu-header">
                                    {{-- <img class="img-80 img-radius" src="{{url('assets/images/avatar-4.jpg')}}" alt="User-Profile-Image"> --}}
                                    {{-- <div class="user-details">
                                        <span id="more-details">John Doe<i class="fa fa-caret-down"></i></span>
                                    </div> --}}
                                </div>
                                <div class="main-menu-content">
                                    <ul>
                                        <li class="more-details">
                                            <a href="{{ url('sample/user-profile') }}"><i class="ti-user"></i>View Profile</a>
                                            <a href="#!"><i class="ti-settings"></i>Settings</a>
                                            <a href="{{ url('sample/auth-normal-sign-in') }}"><i class="ti-layout-sidebar-left"></i>Logout</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            {{--
                            <div class="p-15 p-b-0">
                                <form class="form-material">
                                    <div class="form-group form-primary">
                                        <input type="text" name="footer-email" class="form-control">
                                        <span class="form-bar"></span>
                                        <label class="float-label"><i class="fa fa-search m-r-10"></i>Search Friend</label>
                                    </div>
                                </form>
                            </div>
                            --}}

                            <!-- Menu Starts -->
                            @for($i = 0; $i < FC::Count($menu_array); $i++)
                                @if($menu_array[$i]->menu_depth == "1")
                                    @if($menu_array[$i]->child_node == "Y")
                            {{-- <div class="pcoded-navigation-label" data-sec_depth="{{$menu_array[$i]->menu_id}}">{{ $menu_array[$i]->menu_name }}</div> --}}
                                    @endif
                                @elseif($menu_array[$i]->menu_depth == "2")
                            <ul class="pcoded-item pcoded-left-item" data-sec_depth="{{$menu_array[$i]->parent_menu_id}}">
                                {{--<li class="{{$menu_array[$i]->child_node == "Y" ? "pcoded-hasmenu " : ""}}{{(strlen($menu_array[$i]->menu_url) > 0 && strpos("/".$menu_array[$i]->menu_url, $current_path) !== false) ? "active pcoded-trigger":""}}">--}}
                                <li class="{{$menu_array[$i]->child_node == "Y" ? "pcoded-hasmenu " : ""}}{{($menu_array[$i]->child_node == "Y" ? $menu_array[$i]->menu_id == $current_parent_menu_id : strlen($menu_array[$i]->menu_url) > 0 && $menu_array[$i]->menu_url == $current_path) ? "active pcoded-trigger":""}}">
                                    <a href="{{$menu_array[$i]->child_node == "Y" && $menu_array[$i]->menu_url == "" ? "javascript:void(0)" : url($menu_array[$i]->menu_url)}}" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="{{$menu_array[$i]->menu_icon}}"></i></span>
                                        <span class="pcoded-mtext">{{ $menu_array[$i]->menu_name }}</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                    @if($menu_array[$i]->child_node != "Y")
                                </li>
                            </ul>
                                    @else
                                    <ul class="pcoded-submenu">
                                    @endif
                                @else
                                        {{--<li class="{{(strlen($menu_array[$i]->menu_url) > 0 && strpos("/".$menu_array[$i]->menu_url, $current_path) !== false) ? "active":"" }}">--}}
                                        <li class="{{(strlen($menu_array[$i]->menu_url) > 0 && $menu_array[$i]->menu_url == $current_path) ? "active":"" }}">
                                            <a href="{{ url($menu_array[$i]->menu_url) }}" class="waves-effect waves-dark">
                                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                                <span class="pcoded-mtext">{{ $menu_array[$i]->menu_name }}</span>
                                                <span class="pcoded-mcaret"></span>
                                            </a>
                                        </li>
                                    @if($menu_array[$i]->mn_desc == "1")
                                    </ul>
                                </li>
                            </ul>
                                    @endif
                                @endif
                            @endfor
                            <!-- Menu Ends -->
                        </div>
                    </nav>
                    <div class="pcoded-content">
                        <!-- Page-header start -->
                        @php
                        $current_menu_array = explode("|", $current_menu_str);
                        @endphp
                        <div id="head_all_wrapper" class="page-header">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    @if($menu_array[1]->menu_id != 158)
                                        <div class="col-md-6">
                                    @else
                                        <div class="col-md-12 text-center">
                                    @endif
                                        <div class="page-header-title">
                                            <h5 class="m-b-10" style="margin-bottom: 0px;">{{$current_menu_name}}</h5>
                                        </div>
                                    </div>
                                    @if($menu_array[1]->menu_id != 158)
                                    <div class="col-md-6">
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a href="{{ url('home/index') }}"> <i class="fa fa-home"></i> </a>
                                            </li>
                                            @if(strlen($current_menu_str) > 0)
                                            @for($i = 0; $i < FC::Count($current_menu_array); $i++)
                                                @if($i == FC::Count($current_menu_array) - 1)
                                                <li class="breadcrumb-item"><a href="{{url($current_path)}}">{{$current_menu_array[$i]}}</a></li>
                                                @else
                                                <li class="breadcrumb-item">{{$current_menu_array[$i]}}</li>
                                                @endif
                                            @endfor
                                            @endif
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Page-header end -->
                        <div class="pcoded-inner-content" id="body_content_wrapper">
                            <!-- Main-body start -->
                            <div class="main-body">
                                <div class="page-wrapper">
                                    <!-- Page-body start -->
                                        @yield('content')
                                    <!-- Page-body end -->
                                </div>
                                <div id="styleSelector"> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="entire_loading_image">
        <div class="loading_image_bg"></div>
        <div class="loading_image_fg_wrap">
            <div class="loading_image_fg">
                <i class="fas fa-spinner fa-spin fa-5x" style="color:#fff;"></i>
            </div>
        </div>
    </div>
    <!-- Warning Section Starts -->
    <!-- Older IE warning message -->
    <!--[if lt IE 10]>
<div class="ie-warning">
    <h1>Warning!!</h1>
    <p>You are using an outdated version of Internet Explorer, please upgrade <br/>to any of the following web browsers to access this website.</p>
    <div class="iew-container">
        <ul class="iew-download">
            <li>
                <a href="http://www.google.com/chrome/">
                    <img src="assets/images/browser/chrome.png" alt="Chrome">
                    <div>Chrome</div>
                </a>
            </li>
            <li>
                <a href="https://www.mozilla.org/en-US/firefox/new/">
                    <img src="assets/images/browser/firefox.png" alt="Firefox">
                    <div>Firefox</div>
                </a>
            </li>
            <li>
                <a href="http://www.opera.com">
                    <img src="assets/images/browser/opera.png" alt="Opera">
                    <div>Opera</div>
                </a>
            </li>
            <li>
                <a href="https://www.apple.com/safari/">
                    <img src="assets/images/browser/safari.png" alt="Safari">
                    <div>Safari</div>
                </a>
            </li>
            <li>
                <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                    <img src="assets/images/browser/ie.png" alt="">
                    <div>IE (9 & above)</div>
                </a>
            </li>
        </ul>
    </div>
    <p>Sorry for the inconvenience!</p>
</div>
<![endif]-->
    <!-- Warning Section Ends -->
</body>
</html>
