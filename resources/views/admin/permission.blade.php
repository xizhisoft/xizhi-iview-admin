@extends('admin.layouts.adminbase')

@section('my_title')
Admin(Permission) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<Divider orientation="left">Permission Management</Divider>

<Tabs type="card" v-model="currenttabs">
	<Tab-pane label="Permission List">
	
		<Collapse v-model="collapse_query">
			<Panel name="1">
				Permission Query Filter
				<p slot="content">
				
					<i-row :gutter="16">
						<i-col span="4">
							name&nbsp;&nbsp;
							<i-input v-model.lazy="queryfilter_name" @on-change="permissiongets(page_current, page_last)" size="small" clearable style="width: 100px"></i-input>
						</i-col>
						<i-col span="20">
							&nbsp;
						</i-col>
					</i-row>
				
				
				&nbsp;
				</p>
			</Panel>
		</Collapse>
		<br>
		
		<i-row :gutter="16">
			<br>
			<i-col span="3">
				<i-button @click="ondelete_permission()" :disabled="delete_disabled_permission" type="warning" size="small">Delete</i-button>&nbsp;<br>&nbsp;
			</i-col>
			<i-col span="2">
				<i-button type="default" size="small" @click="oncreate_permission()"><Icon type="ios-color-wand-outline"></Icon> 新建权限</i-button>
			</i-col>
			<i-col span="2">
				<i-button type="default" size="small" @click="onexport_permission()"><Icon type="ios-download-outline"></Icon> 导出权限</i-button>
			</i-col>
			<i-col span="2">
			&nbsp;
			</i-col>
			<i-col span="15">
			&nbsp;
				<Tooltip content="输入用户选择" placement="top">
					<i-select v-model.lazy="test_user_select" filterable remote :remote-method="remoteMethod_sync_user" :loading="test_user_loading" @on-change="" clearable placeholder="输入用户" style="width: 200px;" size="small">
						<i-option v-for="item in test_user_options" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
					</i-select>
				</Tooltip>
				&nbsp;<Icon type="md-arrow-round-forward"></Icon>&nbsp;
				<Tooltip content="输入权限选择" placement="top">
					<i-select v-model.lazy="test_permission_select" filterable remote :remote-method="remoteMethod_sync_permission" :loading="test_permission_loading" @on-change="" clearable placeholder="输入权限" style="width: 200px;" size="small">
						<i-option v-for="item in test_permission_options" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
					</i-select>
				</Tooltip>
				&nbsp;&nbsp;
				<i-button type="default" size="small" @click="testuserspermission"><Icon type="md-help"></Icon> 测试用户是否有权限</i-button>
			</i-col>
		</i-row>
		
		<i-row :gutter="16">
			<i-col span="24">
	
				<i-table height="300" size="small" border :columns="tablecolumns" :data="tabledata" @on-selection-change="selection => onselectchange(selection)"></i-table>
				<br><Page :current="page_current" :total="page_total" :page-size="page_size" @on-change="currentpage => oncurrentpagechange(currentpage)" @on-page-size-change="pagesize => onpagesizechange(pagesize)" :page-size-opts="[5, 10, 20, 50]" show-total show-elevator show-sizer></Page>
			
				<Modal v-model="modal_permission_add" @on-ok="oncreate_permission_ok" ok-text="新建" title="Create - Permission" width="420">
					<div style="text-align:left">
						
						<p>
							name&nbsp;&nbsp;
							<i-input v-model.lazy="permission_add_name" placeholder="" size="small" clearable style="width: 240px"></i-input>

						</p>
						
						&nbsp;
					
					</div>	
				</Modal>
				
				<Modal v-model="modal_permission_edit" @on-ok="permission_edit_ok" ok-text="保存" title="Edit - Permission" width="420">
					<div style="text-align:left">
						
						<p>
							name&nbsp;&nbsp;
							<i-input v-model.lazy="permission_edit_name" placeholder="" size="small" clearable style="width: 240px"></i-input>

						</p>
						
						&nbsp;
					
					</div>	
				</Modal>
		
			</i-col>
		</i-row>

	
	</Tab-pane>

	<Tab-pane label="Advance">

		<i-row :gutter="16">
			<i-col span="9">
				<i-select v-model.lazy="role_select" filterable remote :remote-method="remoteMethod_role" :loading="role_loading" @on-change="onchange_role" clearable placeholder="输入角色名称后选择" style="width: 280px;">
					<i-option v-for="item in role_options" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
				</i-select>
				&nbsp;&nbsp;
				<i-button type="primary" :disabled="boo_update" @click="roleupdatepermission">Update</i-button>
			</i-col>
			<i-col span="6">
				&nbsp;
			</i-col>
			<i-col span="6">
				<i-select v-model.lazy="permission2role_select" filterable remote :remote-method="remoteMethod_permission2role" :loading="permission2role_loading" @on-change="onchange_permission2role" clearable placeholder="输入权限名称查看哪些角色正在使用">
					<i-option v-for="item in permission2role_options" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
				</i-select>
			</i-col>
			<i-col span="3">
				&nbsp;
			</i-col>
		</i-row>
		
		<br><br><br>
			
		<i-row :gutter="16">
			<i-col span="14">
				<Transfer
					:titles="titlestransfer"
					:data="datatransfer"
					filterable
					:target-keys="targetkeystransfer"
					:render-format="rendertransfer"
					@on-change="onChangeTransfer">
				</Transfer>
			</i-col>
			<i-col span="1">
			&nbsp;
			</i-col>
			<i-col span="6">
				<i-input v-model.lazy="permission2role_input" type="textarea" :rows="14" placeholder="" :readonly="true"></i-input>
			</i-col>
			<i-col span="3">
			&nbsp;
			</i-col>
		</i-row>

	</Tab-pane>

</Tabs>
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
		
		sideractivename: '3-3',
		sideropennames: ['3'],
		
		tablecolumns: [
			{
				type: 'selection',
				width: 50,
				align: 'center',
				fixed: 'left'
			},
			{
				type: 'index',
				align: 'center',
				width: 60,
			},
			{
				title: 'id',
				key: 'id',
				sortable: true,
				width: 80
			},
			{
				title: 'name',
				key: 'name',
				width: 240
			},
			{
				title: 'guard_name',
				key: 'guard_name',
				width: 120
			},
			{
				title: 'created_at',
				key: 'created_at',
				width: 160
			},
			{
				title: 'updated_at',
				key: 'updated_at',
				width: 160
			},
			{
				title: 'Action',
				key: 'action',
				align: 'center',
				width: 80,
				render: (h, params) => {
					return h('div', [
						h('Button', {
							props: {
								type: 'primary',
								size: 'small'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.permission_edit(params.row)
								}
							}
						}, 'Edit')
					]);
				},
				fixed: 'right'
			}
		],
		tabledata: [],
		tableselect: [],
		
		//分页
		page_current: 1,
		page_total: 1, // 记录总数，非总页数
		page_size: {{ $config['PERPAGE_RECORDS_FOR_PERMISSION'] }},
		page_last: 1,
		
		// 创建
		modal_permission_add: false,
		permission_add_id: '',
		permission_add_name: '',
		permission_add_email: '',
		permission_add_password: '',
		
		// 编辑
		modal_permission_edit: false,
		permission_edit_id: '',
		permission_edit_name: '',
		permission_edit_email: '',
		permission_edit_password: '',
		
		// 删除
		delete_disabled_permission: true,

		// tabs索引
		currenttabs: 0,
		
		// 查询过滤器
		queryfilter_name: '',
		
		// 查询过滤器下拉
		collapse_query: '',		
		
		// 选择角色查看编辑相应权限
		role_select: '',
		role_options: [],
		role_loading: false,
		boo_update: false,
		titlestransfer: ['待选', '已选'], // ['源列表', '目的列表']
		datatransfer: [],
		targetkeystransfer: [], // ['1', '2'] key
		
		// 选择权限查看哪些角色在使用
		permission2role_select: '',
		permission2role_options: [],
		permission2role_loading: false,
		permission2role_input: '',		
		
		// 测试用户是否有相应权限
		test_permission_select: '',
		test_permission_options: [],
		test_permission_loading: false,
		test_user_select: '',
		test_user_options: [],
		test_user_loading: false,
		
		
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
		
		// 把laravel返回的结果转换成select能接受的格式
		json2selectvalue: function (json) {
			var arr = [];
			for (var key in json) {
				// alert(key);
				// alert(json[key]);
				// arr.push({ obj.['value'] = key, obj.['label'] = json[key] });
				arr.push({ value: key, label: json[key] });
			}
			return arr;
			// return arr.reverse();
		},
		
		// 穿梭框显示文本转换
		json2transfer: function (json) {
			var arr = [];
			for (var key in json) {
				arr.push({
					key: key,
					label: json[key],
					description: json[key],
					disabled: false
				});
			}
			return arr.reverse();
		},
		
		// 穿梭框目标文本转换（数字转字符串）
		arr2target: function (arr) {
			var res = [];
			arr.map(function( value, index) {
				// console.log('map遍历:'+index+'--'+value);
				res.push(value.toString());
			});
			return res;
		},
		
		// 切换当前页
		oncurrentpagechange: function (currentpage) {
			this.permissiongets(currentpage, this.page_last);
		},
		// 切换页记录数
		onpagesizechange: function (pagesize) {
			var _this = this;
			var cfg_data = {};
			cfg_data['PERPAGE_RECORDS_FOR_PERMISSION'] = pagesize;
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
					_this.page_size = pagesize;
					_this.permissiongets(1, _this.page_last);
				} else {
					_this.warning(false, 'Warning', 'failed!');
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', 'failed!');
			})
		},		
		
		permissiongets: function(page, last_page){
			var _this = this;
			
			if (page > last_page) {
				page = last_page;
			} else if (page < 1) {
				page = 1;
			}
			
			// var queryfilter_logintime = [];

			// for (var i in _this.queryfilter_logintime) {
				// if (typeof(_this.queryfilter_logintime[i])!='string') {
					// queryfilter_logintime.push(_this.queryfilter_logintime[i].Format("yyyy-MM-dd"));
				// } else if (_this.queryfilter_logintime[i] == '') {
					// queryfilter_logintime = ['1970-01-01', '9999-12-31'];
					// break;
				// } else {
					// queryfilter_logintime.push(_this.queryfilter_logintime[i]);
				// }
			// }
			// console.log(queryfilter_logintime);

			var queryfilter_name = _this.queryfilter_name;

			_this.loadingbarstart();
			var url = "{{ route('admin.permission.permissiongets') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					perPage: _this.page_size,
					page: page,
					queryfilter_name: queryfilter_name,
				}
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}

				if (response.data) {
					_this.delete_disabled_permission = true;
					_this.tableselect = [];
					
					_this.page_current = response.data.current_page;
					_this.page_total = response.data.total;
					_this.page_last = response.data.last_page;
					_this.tabledata = response.data.data;
					
				}
				
				_this.loadingbarfinish();
			})
			.catch(function (error) {
				_this.loadingbarerror();
				_this.error(false, 'Error', error);
			})
		},
		
		// 表role选择
		onselectchange: function (selection) {
			var _this = this;
			_this.tableselect = [];

			for (var i in selection) {
				_this.tableselect.push(selection[i].id);
			}
			
			_this.delete_disabled_permission = _this.tableselect[0] == undefined ? true : false;
		},

		// permission编辑前查看
		permission_edit: function (row) {
			var _this = this;
			
			_this.permission_edit_id = row.id;
			_this.permission_edit_name = row.name;
			// _this.role_edit_email = row.email;
			// _this.user_edit_password = row.password;
			// _this.relation_xuqiushuliang_edit[0] = row.xuqiushuliang;
			// _this.relation_xuqiushuliang_edit[1] = row.xuqiushuliang;
			// _this.user_created_at_edit = row.created_at;
			// _this.user_updated_at_edit = row.updated_at;

			_this.modal_permission_edit = true;
		},		
		

		// permission编辑后保存
		permission_edit_ok: function () {
			var _this = this;
			
			var id = _this.permission_edit_id;
			var name = _this.permission_edit_name;
			// var email = _this.user_edit_email;
			// var password = _this.user_edit_password;
			// var created_at = _this.relation_created_at_edit;
			// var updated_at = _this.relation_updated_at_edit;
			
			if (name == '' || name == null || name == undefined) {
				_this.warning(false, '警告', '内容不能为空！');
				return false;
			}
			
			// var regexp = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/;
			// if (! regexp.test(email)) {
				// _this.warning(false, 'Warning', 'Email is incorrect!');
				// return false;
			// }
			
			var url = "{{ route('admin.permission.update') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				id: id,
				name: name,
				// email: email,
				// password: password,
				// xuqiushuliang: xuqiushuliang[1],
				// created_at: created_at,
				// updated_at: updated_at
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
				_this.permissiongets(_this.page_current, _this.page_last);
				
				if (response.data) {
					_this.success(false, '成功', '更新成功！');
					
					_this.permission_edit_id = '';
					_this.permission_edit_name = '';
					// _this.role_edit_email = '';
					// _this.role_edit_password = '';
					
					// _this.relation_xuqiushuliang_edit = [0, 0];
					// _this.relation_created_at_edit = '';
					// _this.relation_updated_at_edit = '';
				} else {
					_this.error(false, '失败', '更新失败！请刷新查询条件后再试！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '更新失败！');
			})			
		},
		
		// ondelete_permission
		ondelete_permission: function () {
			var _this = this;
			
			var tableselect = _this.tableselect;
			
			if (tableselect[0] == undefined) return false;
			
			var url = "{{ route('admin.permission.permissiondelete') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				tableselect: tableselect
			})
			.then(function (response) {
				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
				if (response.data) {
					_this.permissiongets(_this.page_current, _this.page_last);
					_this.success(false, '成功', '删除成功！');
				} else {
					_this.error(false, '失败', '删除失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '删除失败！');
			})
		},		
		
		// 显示新建权限
		oncreate_permission: function () {
			this.modal_permission_add = true;
		},
		
		// 新建权限
		oncreate_permission_ok: function () {
			var _this = this;
			var name = _this.permission_add_name;
			
			if (name == '' || name == null || name == undefined) {
				_this.warning(false, '警告', '内容不能为空！');
				return false;
			}
			
			// var re = new RegExp(“a”);  //RegExp对象。参数就是我们想要制定的规则。有一种情况必须用这种方式，下面会提到。
			// var re = /a/;   // 简写方法 推荐使用 性能更好  不能为空 不然以为是注释 ，
			// var regexp = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/;
			// if (! regexp.test(email)) {
				// _this.warning(false, 'Warning', 'Email is incorrect!');
				// return false;
			// }

			var url = "{{ route('admin.permission.create') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				name: name,
			})
			.then(function (response) {
				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
				if (response.data) {
					_this.success(false, 'Success', 'Permission created successfully!');
					_this.permission_add_name = '';
					_this.permissiongets(_this.page_current, _this.page_last);
				} else {
					_this.error(false, 'Warning', 'Permission created failed!');
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', 'Permission created failed!');
			})
		},		
		
		// 导出权限
		onexport_permission: function(){
			var url = "{{ route('admin.permission.excelexport') }}";
			window.setTimeout(function(){
				window.location.href = url;
			}, 1000);
			return false;
		},		
		
		// 穿梭框显示文本
		rendertransfer: function (item) {
			return item.label + ' (ID:' + item.key + ')';
		},
		
		onChangeTransfer: function (newTargetKeys, direction, moveKeys) {
			// console.log(newTargetKeys);
			// console.log(direction);
			// console.log(moveKeys);
			this.targetkeystransfer = newTargetKeys;
		},		
		
		
		// 选择role查看permission
		onchange_role: function () {
			var _this = this;
			var roleid = _this.role_select;
			// console.log(roleid);return false;
			
			if (roleid == undefined || roleid == '') {
				_this.targetkeystransfer = [];
				_this.datatransfer = [];
				_this.boo_update = true;
				return false;
			}
			_this.boo_update = false;
			var url = "{{ route('admin.permission.rolehaspermission') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					roleid: roleid
				}
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
				if (response.data) {
					var json = response.data.allpermissions;
					_this.datatransfer = _this.json2transfer(json);
					
					var arr = response.data.rolehaspermission;
					_this.targetkeystransfer = _this.arr2target(arr);

				} else {
					_this.targetkeystransfer = [];
					_this.datatransfer = [];
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
			
		},
		
		// roleupdatepermission
		roleupdatepermission: function () {
			var _this = this;
			var roleid = _this.role_select;
			var permissionid = _this.targetkeystransfer;

			if (roleid == undefined || roleid == '') return false;
			
			var url = "{{ route('admin.permission.roleupdatepermission') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				roleid: roleid,
				permissionid: permissionid
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
				if (response.data) {
					_this.success(false, 'Success', 'Update OK!');
				} else {
					_this.warning(false, 'Warning', 'Update failed!');
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
		},

		// 远程查询角色
		remoteMethod_role (query) {
			var _this = this;

			if (query !== '') {
				_this.role_loading = true;
				
				var queryfilter_name = query;
				
				var url = "{{ route('admin.permission.rolelist') }}";
				axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
				axios.get(url,{
					params: {
						queryfilter_name: queryfilter_name
					}
				})
				.then(function (response) {

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
					
					if (response.data) {
						var json = response.data;
						_this.role_options = _this.json2selectvalue(json);
					}
				})
				.catch(function (error) {
				})				
				
				setTimeout(() => {
					_this.role_loading = false;
					// const list = this.list.map(item => {
						// return {
							// value: item,
							// label: item
						// };
					// });
					// this.options1 = list.filter(item => item.label.toLowerCase().indexOf(query.toLowerCase()) > -1);
				}, 200);
			} else {
				_this.slot_options = [];
			}
		},
		
		
		// 选择permission查看role
		onchange_permission2role: function () {
			var _this = this;
			var permissionid = _this.permission2role_select;
			// console.log(permissionid);return false;
			
			if (permissionid == undefined || permissionid == '') {
				_this.permission2role_input = '';
				return false;
			}

			var url = "{{ route('admin.permission.permissiontoviewrole') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					permissionid: permissionid
				}
			})
			.then(function (response) {

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
				if (response.data) {
					var json = response.data;
					var str = '';
					for (var key in json) {
						str += json[key] + '\n';
					}
					// _this.permission2role_input = str.slice(0, -2);
					_this.permission2role_input = str.replace(/\n$/, '');
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
		},		

		// 远程查询权限
		remoteMethod_permission2role (query) {
			var _this = this;

			if (query !== '') {
				_this.permission2role_loading = true;
				
				var queryfilter_name = query;
				
				var url = "{{ route('admin.permission.permissionlist') }}";
				axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
				axios.get(url,{
					params: {
						queryfilter_name: queryfilter_name
					}
				})
				.then(function (response) {
					if (response.data['jwt'] == 'logout') {
						_this.alert_logout();
						return false;
					}
					
					if (response.data) {
						var json = response.data;
						_this.permission2role_options = _this.json2selectvalue(json);
					}
				})
				.catch(function (error) {
				})				
				
				setTimeout(() => {
					_this.permission2role_loading = false;
				}, 200);
			} else {
				_this.permission2role_options = [];
			}
		},

		
		// 远程查询权限（同步）
		remoteMethod_sync_permission (query) {
			var _this = this;
			if (query !== '') {
				_this.test_permission_loading = true;
				var queryfilter_name = query;
				var url = "{{ route('admin.permission.permissionlist') }}";
				axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
				axios.get(url,{
					params: {
						queryfilter_name: queryfilter_name
					}
				})
				.then(function (response) {
					if (response.data['jwt'] == 'logout') {
						_this.alert_logout();
						return false;
					}
					
					if (response.data) {
						var json = response.data;
						_this.test_permission_options = _this.json2selectvalue(json);
					}
				})
				.catch(function (error) {
				})				
				setTimeout(() => {
					_this.test_permission_loading = false;
				}, 200);
			} else {
				_this.test_permission_options = [];
			}
		},
		
		
		// 远程查询用户（同步）
		remoteMethod_sync_user (query) {
			var _this = this;
			if (query !== '') {
				_this.test_user_loading = true;
				var queryfilter_name = query;
				var url = "{{ route('admin.permission.userlist') }}";
				axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
				axios.get(url,{
					params: {
						queryfilter_name: queryfilter_name
					}
				})
				.then(function (response) {
					if (response.data['jwt'] == 'logout') {
						_this.alert_logout();
						return false;
					}
					
					if (response.data) {
						var json = response.data;
						_this.test_user_options = _this.json2selectvalue(json);
					}
				})
				.catch(function (error) {
				})				
				setTimeout(() => {
					_this.test_user_loading = false;
				}, 200);
			} else {
				_this.test_user_options = [];
			}
		},		
		

		// 测试用户是否有权限
		testuserspermission: function () {
			var _this = this;
			var permissionid = _this.test_permission_select;
			var userid = _this.test_user_select;

			if (userid == undefined || userid == '' ||
				permissionid == undefined || permissionid == '') {
				_this.warning(false, 'Warning', '内容不能为空！');
				return false;
			}
			
			var url = "{{ route('admin.permission.testuserspermission') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				permissionid: permissionid,
				userid: userid
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
				if (response.data) {
					_this.success(false, 'Success', 'Permission(s) test successfully!');
				} else {
					_this.warning(false, 'Warning', 'Permission(s) test failed!');
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', 'Permission(s) test failed!');
			})
		},

	},
	mounted: function(){
		var _this = this;
		_this.current_nav = '权限管理';
		_this.current_subnav = '权限';
		// 显示所有
		_this.permissiongets(1, 1); // page: 1, last_page: 1
	}
});
</script>
@endsection