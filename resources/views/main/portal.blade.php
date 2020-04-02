@extends('main.layouts.mainbase')

@section('my_title')
Main(Portal) - 
@parent
@endsection

@section('my_style')
<style>
.ivu-table td.tableclass1{
	background-color: #2db7f5;
	color: #fff;
}
</style>
@endsection

@section('my_js')
@endsection

@section('my_project')
<strong>{{$config['SITE_TITLE']}} - Portal</strong>
@endsection

@section('my_body')
@parent

<div id="app" v-cloak>

	<i-row :gutter="16">
		<i-col span="6">

			<Card>
				<p slot="title">
					生产管理模块（2020版）
					@hasanyrole('role_smt_config|role_super_admin')
					<span style="float:right">
						<a href="#" target="_blank">配置</a>
					</span>
					@endcan
				</p>
				<p v-for="item in CardList_Shengchan">
					<a :href="item.url" target="_blank"><Icon type="ios-link"></Icon>&nbsp;&nbsp;@{{ item.name }}</a>
					<span style="float:right">
						Percent: @{{ item.percent }}%
					</span>
				</p>
			</Card>

		</i-col>
		
		<i-col span="1">
		&nbsp;
		</i-col>
		
		<i-col span="6">
		
			<Card>
				<p slot="title">
					后台管理模块（2020版）
				</p>
				<p v-for="item in CardList_Admin">
					<a :href="item.url" target="_blank"><Icon type="ios-link"></Icon>&nbsp;&nbsp;@{{ item.name }}</a>
					<span style="float:right">
						Percent: @{{ item.percent }}%
					</span>
				</p>
			</Card>
			&nbsp;
		
		</i-col>
		<i-col span="5">
		&nbsp;
		</i-col>
	</i-row>

	<br><br><br>
	<p><br></p><p><br></p><p><br></p>
	<p><br></p><p><br></p><p><br></p>
	<p><br></p><p><br></p><p><br></p>
	<p><br></p><p><br></p><p><br></p>
	<p><br></p><p><br></p><p><br></p>
	<p><br></p><p><br></p><p><br></p>
	<p><br></p><p><br></p><p><br></p>
	<p><br></p><p><br></p><p><br></p>
	


</div>
@endsection

@section('my_js_others')
@parent	
<script>
var vm_app = new Vue({
	el: '#app',
	data: {
		CardList_Shengchan: [
			{
				name: '模块一',
				url: "#",
				percent: 65,
			},
			{
				name: '模块二',
				url: "#",
				percent: 15,
			},
			{
				name: '模块三',
				url: "#",
				percent: 85,
			},
		],


		CardList_Admin: [
			{
				name: '后台管理入口',
				url: "{{ route('admin.system.index') }}",
				percent: 99,
			},
		],
		
		
		
			
			
	},
	methods: {
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
		
		


		
		
		
		
		
			
			
	},
	mounted () {
	}
})
</script>
@endsection