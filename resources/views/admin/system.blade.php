@extends('admin.layouts.adminbase')

@section('my_title')
Admin(System) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent

<div>

	<Divider orientation="left">System Infomation</Divider>

	<i-row>
		<i-col span="12">
            <Card style="width:420px">
                <p slot="title">
                    <Icon type="ios-cog-outline"></Icon>
                    系统环境信息
                </p>
				<p>
					操作系统
					<span style="float:right">
                        <span v-if="systeminfo.os == 'WINNT'">
                            <Icon type="logo-windows"></Icon> Windows
                        </span>
                        <span v-else>
                            <Icon type="logo-tux"></Icon> Linux
                        </span>
					</span>
				</p>
				<p>
                    运行环境
					<span style="float:right">
                    @{{ systeminfo.operating_environment }}
					</span>
				</p>
				<p>
                PHP_SAPI_NAME
					<span style="float:right">
                    @{{ systeminfo.php_sapi_name }}
					</span>
				</p>
				<p>
                最大上传阈值
					<span style="float:right">
                    @{{ systeminfo.upload_max_filesize }}
					</span>
				</p>
				<p>
                最大执行时间
					<span style="float:right">
                    @{{ systeminfo.max_execution_time }}
					</span>
				</p>
				<p>
                服务端日期
					<span style="float:right">
                    @{{ systeminfo.server_date }}
					</span>
				</p>
				<p>
                北京时间
					<span style="float:right">
                    @{{ systeminfo.beijing_time }}
					</span>
				</p>
				<p>
                服务器名称
					<span style="float:right">
                    @{{ systeminfo.server_name }}
					</span>
				</p>
				<p>
                服务器IP
					<span style="float:right">
                    @{{ systeminfo.server_addr }}
					</span>
				</p>
				<p>
                主机地址
					<span style="float:right">
                    @{{ systeminfo.http_host }}
					</span>
				</p>
				<p>
                主机根目录
					<span style="float:right">
                    @{{ systeminfo.document_root }}
					</span>
				</p>
				<p>
                磁盘剩余空间
					<span style="float:right">
                    @{{ systeminfo.disk_free_space }}
					</span>
				</p>
				<p>
                REGISTER_GLOBALS
					<span style="float:right">
                    @{{ systeminfo.register_globals }}
					</span>
				</p>
				<p>
                MAGIC_QUOTES_PGC
					<span style="float:right">
                    @{{ systeminfo.magic_quotes_gpc }}
					</span>
				</p>
				<p>
                MAGIC_QUOTES_RUNTIME
					<span style="float:right">
                    @{{ systeminfo.magic_quotes_runtime }}
					</span>
				</p>
            </Card>
        </i-col>

		<i-col span="12">
			<Card style="width:420px">
                <p slot="title">
					<Icon type="ios-laptop"></Icon>
                    当前客户端
                </p>
				<p>
					<span style="float:right">
                    @{{ systeminfo.http_user_agent }}
					</span>
					&nbsp;
				</p>
			</Card>
        </i-col>

	</i-row>

<br>


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
		
		sideractivename: '1-1',
		sideropennames: ['1'],

		systeminfo: [],
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
		
		systemgets: function() {
			var _this = this;
			var url = "{{ route('admin.system.list') }}";
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
				_this.systeminfo = response.data;
				// _this.systeminfo.total = _this.systeminfo.data.length;
				// alert(_this.systeminfo.total);
				// alert(_this.systeminfo);
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
		_this.current_subnav = '系统信息';
		_this.systemgets();

	}
});
</script>
@endsection