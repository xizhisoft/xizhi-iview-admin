@extends('admin.layouts.adminbase')

@section('my_title')
Admin(Config) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent

<div>

	<Divider orientation="left">Configration Management</Divider>

	<i-row>
		<i-col span="6" v-for="(val, index) in gets">
			<strong>@{{ val.cfg_name }}</strong><br>
			<i-input type="textarea" :id="val.cfg_name" :value="val.cfg_value" @on-blur="event=>configchange(event)" style="width:200px;" placeholder="暂无配置值" size="small" clearable></i-input>
			<p style="color: rgb(128, 132, 143);">&nbsp;@{{ val.cfg_description }}</p><br>
		</i-col>
	</i-row>

</div>
@endsection

@section('my_footer')
@parent

@endsection

@section('my_js_others')
@parent
<script>
var vm_app = new Vue({
    el: '#app',
    data: {
		current_nav: '',
		current_subnav: '',
		
		sideractivename: '1-2',
		sideropennames: ['1'],

		gets: {}
    },
	methods: {
		menuselect: function (name) {
			navmenuselect(name);
		},
		// 1.加载进度条
		loadingbarstart () {
			this.$Loading.start();
		},
		loadingbarfinish () {
			this.$Loading.finish();
		},
		loadingbarerror () {
			this.$Loading.error();
		},
		// 2.Notice 通知提醒
		info (nodesc, title, content) {
			this.$Notice.info({
				title: title,
				desc: nodesc ? '' : content
			});
		},
		success (nodesc, title, content) {
			this.$Notice.success({
				title: title,
				desc: nodesc ? '' : content
			});
		},
		warning (nodesc, title, content) {
			this.$Notice.warning({
				title: title,
				desc: nodesc ? '' : content
			});
		},
		error (nodesc, title, content) {
			this.$Notice.error({
				title: title,
				desc: nodesc ? '' : content
			});
		},

		alert_logout: function () {
			this.error(false, '会话超时', '会话超时，请重新登录！');
			window.setTimeout(function(){
				window.location.href = "{{ route('portal') }}";
			}, 2000);
			return false;
		},
		
		configchange: function(event){
			var _this = this;
			var cfg_name = event.target.offsetParent.id;
			var cfg_value = event.target.value;
			
			var cfg_data = {};
			cfg_data[cfg_name] = cfg_value;
			
			var url = "{{ route('admin.config.change') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
					cfg_data: cfg_data
				})
				.then(function (response) {

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}

				if (response.data) {
						// alert('success');
					} else {
						_this.warning(false, 'Warning', cfg_name + ' failed to be modified!');
						event.target.value = cfg_value;
					}
				})
				.catch(function (error) {
					_this.error(false, 'Error', cfg_name + ' failed to be modified!');
				})
		},
		
		configgets: function() {
			var _this = this;
			var url = "{{ route('admin.config.list') }}";
			_this.loadingbarstart();
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url, {
			})
			.then(function (response) {

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
				//console.log(response);
				_this.gets = response.data;
				// _this.gets.total = _this.gets.data.length;
				// alert(_this.gets.total);
				// alert(_this.gets);
				_this.loadingbarfinish();
			})
			.catch(function (error) {
				_this.loadingbarerror();
			})
		},

	},
	mounted: function(){
		var _this = this;
		_this.current_nav = '配置管理';
		_this.current_subnav = '系统配置';
		_this.configgets();

	}
});
</script>
@endsection