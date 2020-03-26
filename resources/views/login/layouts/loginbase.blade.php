<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<title>
@section('my_title')
{{$SITE_TITLE}}  Ver: {{$SITE_VERSION}}
@show
</title>
<link rel="stylesheet" href="{{ asset('statics/iview/styles/iview.css') }}">
<style type="text/css">
	/* 解决闪烁问题的CSS */
	[v-cloak] {	display: none; }
</style>
<style type="text/css">
.layout{
    border: 1px solid #d7dde4;
    background: #f5f7f9;
    position: relative;
    border-radius: 4px;
    overflow: hidden;
}
.layout-header-bar{
	background: #fff;
	box-shadow: 0 1px 1px rgba(0,0,0,.1);
}
.layout-logo{
    width: 100px;
    height: 30px;
    <!--background: #5b6270;-->
    border-radius: 3px;
    float: left;
    position: relative;
    top: 15px;
    left: 20px;
}
.layout-breadcrumb{
	<!-- padding: 10px 15px 0; -->
    width: 100px;
    height: 30px;
    <!--background: #5b6270;-->
    border-radius: 3px;
    float: left;
    position: relative;
    top: 5px;
    left: 20px;
}
.layout-nav{
	float: right;
	position: relative;
    width: 420px;
    margin: 0 auto;
    margin-right: 10px;
}
.layout-header-center{
    text-align: center;
}
.layout-footer-center{
    text-align: center;
}
.screen_middle{
    position: fixed;
    top: 0px;
    left: 0px;
    right: 0px;
    bottom: 0px;
    margin: auto;
}
</style>
@yield('my_style')
<script src="{{ asset('js/functions.js') }}"></script>
<script>
	checkBrowser();
</script>
<script>
isMobile = mobile();
if (isMobile) {
	// alert('系统暂不支持移动端！');
	// document.execCommand("Stop");
    // window.stop();
    
    // window.setTimeout(function(){
        var url = "{{route('logincube')}}";
        window.location.href = url;
    // }, 1000);
}
</script>
@yield('my_js')
</head>
<body>
<div id="app" v-cloak>
<!-- <br><br><br><br> -->
	<div class="layout screen_middle">
		<Layout>
			<Header class="layout-header-center">
				<!-- 头部 -->
				<br><br><br><br><br>
				@section('my_logo_and_title')
				<!-- <h1>{{$SITE_TITLE}}<br>
				<small>{{$SITE_VERSION}}</small></h1> -->
				@show
				<!-- /头部 -->
			</Header>
			<Layout>
			<Content>
				<!-- 主体 -->
				@section('my_body')
				@show
				<!-- /主体 -->
			</Content>
			</Layout>
			<Footer>
				<!-- 底部 -->
				<Footer class="layout-footer-center">
				@section('my_footer')
				<a href="{{route('portal')}}">{{$SITE_TITLE}}</a>&nbsp;&nbsp;{{$SITE_COPYRIGHT}}
				@show
				</Footer>
				<!-- /底部 -->
			</Footer>
		</Layout>
	</div>
	
</div>
<script src="{{ asset('js/vue.min.js') }}"></script>
<script src="{{ asset('js/axios.min.js') }}"></script>
<script src="{{ asset('js/bluebird.min.js') }}"></script>
<script src="{{ asset('statics/iview/iview.min.js') }}"></script>
@yield('my_js_others')
</body>
</html>