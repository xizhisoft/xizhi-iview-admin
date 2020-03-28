@extends('login.layouts.loginbase')

@section('my_title')
Login - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_logo_and_title')
@parent
@endsection


@section('my_body')
@parent

<br><br><br>

<i-row :gutter="16">
	<i-col span="9">
		&nbsp;
	</i-col>
	<i-col span="6">

		<Card style="width:350px">
			<p slot="title" style="text-align:center">
				{{$SITE_TITLE}}
				<small>{{$SITE_VERSION}}</small>
			</p>

			<p>
				<i-form ref="formInline" :model="formInline" :rules="ruleInline" @submit.native.prevent>
					<Form-item prop="username">
						<i-input ref="ref_username" prefix="ios-contact-outline" type="text" v-model="formInline.username" @on-enter="handleSubmit('formInline')" placeholder="用户名" size="large"></i-input>
					</Form-item>

					<Form-item prop="password">
						<i-input ref="ref_password" prefix="ios-lock-outline" type="password" v-model="formInline.password" @on-enter="handleSubmit('formInline')" placeholder="密码" size="large"></i-input>
					</Form-item>

					<i-row>
						<i-col span="16">
							<Form-item prop="captcha">
								<i-input ref="ref_captcha" prefix="ios-key-outline" type="text" v-model="formInline.captcha" @on-enter="handleSubmit('formInline')" placeholder="验证码" size="large"></i-input>
							</Form-item>
						</i-col>
						<i-col span="8">
							&nbsp;<img ref="captcha" src="{{captcha_src('flat4xizhi')}}" @click="captchaclick" style="cursor:pointer;vertical-align:top;">
						</i-col>
					</i-row>
					
					<br><br>
					
					<i-row>
						<i-col span="16">
							保持登录状态&nbsp;
							<i-switch ref="ref_rememberme" v-model="formInline.rememberme" size="small">
								<span slot="open"></span>
								<span slot="close"></span>
							</i-switch>
						</i-col>
						<i-col span="8">
						&nbsp;
						<!--待完成功能
							<a href="#">Forget?</a>
						-->
						</i-col>
					</i-row>
					
					<br><br><br>
					<Form-item>
					<i-button :disabled="disabled_login_submit" :loading="loading_submit" type="primary" @click="handleSubmit('formInline')" long size="large">登 录</i-button>
					<!-- <br>
					<i-button :disabled="disabled_login_reset" @click="handleReset('formInline')" long size="large">重  置</i-button> -->
					</Form-item>
					
					<div v-html="formInline.loginmessage">@{{ formInline.loginmessage }}</div>
				
				</i-form>

			</p>
		</Card>

	</i-col>

	<i-col span="9">
		&nbsp;
	</i-col>
</i-row>

<br><br><br>

@endsection

@section('my_footer')
<br><br>
@parent
<br><br><br><br>
@endsection

@section('my_js_others')
<script>
var vm_app = new Vue({
    el: '#app',
    data: {
		
		formInline: {
			username: '',
			password: '',
			captcha: '',
			rememberme: false,
			loginmessage: ''
		},
		ruleInline: {
			username: [
				{ required: true, message: '* 请输入用户名', trigger: 'blur' }
			],
			password: [
				{ required: true, message: '* 请输入密码', trigger: 'blur' },
				{ type: 'string', min: 3, message: '* 密码长度至少3位以上', trigger: 'blur' }
			],
			captcha: [
				{ required: true, message: '* 请输入验证码', trigger: 'blur' },
				{ type: 'string', min: 4, message: '* 请输入4位长度的验证码', trigger: 'blur' }
			]
		},

		disabled_login_submit: false,
		disabled_login_reset: false,

		loading_submit: false,
		
    },
	methods: {
		handleSubmit(name) {
			this.$refs[name].validate((valid) => {
				if (valid) {
					var _this = this;

					_this.logindisabled(true);
					_this.$Message.loading('正在验证...');

					if (_this.formInline.username == undefined || _this.formInline.password == undefined || _this.formInline.captcha == undefined ||
						_this.formInline.username == '' || _this.formInline.password == '' || _this.formInline.captcha == '') {
						_this.$Message.warning('内容未填写完整！');
						_this.logindisabled(false);
						return false;
					}
					
					var url = "{{ route('login.checklogin') }}";
					axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
					axios.post(url, {
						name: _this.formInline.username,
						password: _this.formInline.password,
						captcha: _this.formInline.captcha,
						rememberme: _this.formInline.rememberme
					})
					.then(function (response) {
						
						if (response.data) {
							_this.formInline.password = '**********';
							_this.$Message.success('登录成功！ 正在跳转...');
							window.setTimeout(function(){
								_this.loginreset;
								var url = "{{ route('portal') }}";
								window.location.href = url;
								_this.formInline.loginmessage = '';
							}, 1500);
						} else {
							_this.$Message.warning('验证码错误或登录失败！');
							_this.logindisabled(false);
						}
					})
					.catch(function (error) {
						_this.$Message.error('用户状态过期或其他未知错误！');
						_this.logindisabled(false);
					})
					_this.captchaclick();
				}
			})
		},
		handleReset(name) {
			this.$refs[name].resetFields();
		},
		captchaclick() {
			this.$refs.captcha.src+=Math.random().toString().substr(-1);
		},
		logindisabled(value) {
			if (value) {
				this.$refs.ref_username.disabled = true;
				this.$refs.ref_password.disabled = true;
				this.$refs.ref_captcha.disabled = true;
				this.$refs.ref_rememberme.disabled = true;
				this.disabled_login_submit = true;
				this.disabled_login_reset = true;
				this.loading_submit = true;
			} else {
				this.$refs.ref_username.disabled = false;
				this.$refs.ref_password.disabled = false;
				this.$refs.ref_captcha.disabled = false;
				this.$refs.ref_rememberme.disabled = false;
				this.disabled_login_submit = false;
				this.disabled_login_reset = false;
				this.loading_submit = false;
			}
		},
	}
});
</script>
@endsection