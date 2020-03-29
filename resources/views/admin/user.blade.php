@extends('admin.layouts.adminbase')

@section('my_title')
Admin(User) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent

<Divider orientation="left">User Management</Divider>

<Tabs type="card" v-model="currenttabs">
	<Tab-pane label="User List">
	
		<Collapse v-model="collapse_query">
			<Panel name="1">
				User Query Filter
				<p slot="content">
				
					<i-row :gutter="16">
						<i-col span="8">
							* login time&nbsp;&nbsp;
							<Date-picker v-model.lazy="queryfilter_logintime" @on-change="usergets(page_current, page_last);onselectchange();" type="daterange" size="small" placement="top" style="width:200px"></Date-picker>
						</i-col>
						<i-col span="4">
							name&nbsp;&nbsp;
							<i-input v-model.lazy="queryfilter_name" @on-change="usergets(page_current, page_last)" size="small" clearable style="width: 100px"></i-input>
						</i-col>
						<i-col span="4">
							email&nbsp;&nbsp;
							<i-input v-model.lazy="queryfilter_email" @on-change="usergets(page_current, page_last)" size="small" clearable style="width: 100px"></i-input>
						</i-col>
						<i-col span="4">
							login ip&nbsp;&nbsp;
							<i-input v-model.lazy="queryfilter_loginip" @on-change="usergets(page_current, page_last)" size="small" clearable style="width: 100px"></i-input>
						</i-col>
						<i-col span="4">
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
				<i-button @click="ondelete_user()" :disabled="delete_disabled_user" type="warning" size="small">Delete</i-button>&nbsp;<br>&nbsp;
			</i-col>
			<i-col span="2">
				<i-button type="default" size="small" @click="oncreate_user()"><Icon type="ios-color-wand-outline"></Icon> 新建用户</i-button>
			</i-col>
			<i-col span="2">
				<i-button type="default" size="small" @click="onexport_user()"><Icon type="ios-download-outline"></Icon> 导出用户</i-button>
			</i-col>
			<i-col span="17">
				&nbsp;
			</i-col>
		</i-row>
		
		<i-row :gutter="16">
			<i-col span="24">
	
				<i-table height="300" size="small" border :columns="tablecolumns" :data="tabledata" @on-selection-change="selection => onselectchange(selection)"></i-table>
				<br><Page :current="page_current" :total="page_total" :page-size="page_size" @on-change="currentpage => oncurrentpagechange(currentpage)" @on-page-size-change="pagesize => onpagesizechange(pagesize)" :page-size-opts="[5, 10, 20, 50]" show-total show-elevator show-sizer></Page>
			
				<Modal v-model="modal_user_add" @on-ok="oncreate_user_ok" ok-text="新建" title="Create - User" width="460">
					<div style="text-align:left">
						
						<p>
							name&nbsp;&nbsp;
							<i-input v-model.lazy="user_add_name" placeholder="登录名称或工号" size="small" clearable style="width: 120px"></i-input>

							&nbsp;&nbsp;&nbsp;&nbsp;

							department&nbsp;&nbsp;
							<i-input v-model.lazy="user_add_department" placeholder="部门" size="small" clearable style="width: 120px"></i-input>
							
							<br><br>

							uid&nbsp;&nbsp;
							<i-input v-model.lazy="user_add_uid" placeholder="工号" size="small" clearable style="width: 120px"></i-input>

							&nbsp;&nbsp;&nbsp;&nbsp;

							displayname&nbsp;&nbsp;
							<i-input v-model.lazy="user_add_displayname" placeholder="显示名称" size="small" clearable style="width: 120px"></i-input>

							<br><br>

							email&nbsp;&nbsp;
							<i-input v-model.lazy="user_add_email" placeholder="邮箱地址" size="small" clearable style="width: 120px"></i-input>

							<br><br>

							password&nbsp;&nbsp;
							<i-input v-model.lazy="user_add_password" placeholder="" size="small" clearable style="width: 120px" type="password"></i-input>
							&nbsp;*默认密码为12345678

						</p>
						
						&nbsp;
					
					</div>	
				</Modal>
				
				<Modal v-model="modal_user_edit" @on-ok="user_edit_ok" ok-text="保存" title="Edit - User" width="460">
					<div style="text-align:left">
						
						<p>
							name&nbsp;&nbsp;
							<i-input v-model.lazy="user_edit_name" placeholder="登录名称或工号" size="small" clearable style="width: 120px"></i-input>

							&nbsp;&nbsp;&nbsp;&nbsp;

							department&nbsp;&nbsp;
							<i-input v-model.lazy="user_edit_department" placeholder="部门" size="small" clearable style="width: 120px"></i-input>
							
							<br><br>

							uid&nbsp;&nbsp;
							<i-input v-model.lazy="user_edit_uid" placeholder="工号" size="small" clearable style="width: 120px"></i-input>

							&nbsp;&nbsp;&nbsp;&nbsp;

							displayname&nbsp;&nbsp;
							<i-input v-model.lazy="user_edit_displayname" placeholder="显示名称" size="small" clearable style="width: 120px"></i-input>
							
							<br><br>

							email&nbsp;&nbsp;
							<i-input v-model.lazy="user_edit_email" placeholder="邮箱地址" size="small" clearable style="width: 120px"></i-input>
							
							<br><br>

							password&nbsp;&nbsp;
							<i-input v-model.lazy="user_edit_password" placeholder="不修改密码请留空" size="small" clearable style="width: 120px" type="password"></i-input>

						</p>
						
						&nbsp;
					
					</div>	
				</Modal>
		
			</i-col>
		</i-row>

	</Tab-pane>

	<Tab-pane label="申请流程">
		<Tabs type="card" v-model="currentsubtabs1" :animated="false">

		<Tab-pane label="批量指定处理用户（申请）">
			<i-row :gutter="16">
				<i-col span="24">
					<font color="#ff9900">* 在此指定哪些用户可以处理“当前用户”提交的申请。</font>
					&nbsp;
				</i-col>
			</i-row>
			
			<br><br>
			<i-row :gutter="16">
				<i-col span="5">
					当前代理申请用户：&nbsp;<strong>@{{ username_current1 }}</strong>
					
				</i-col>
				<i-col span="19">
				&nbsp;&nbsp;<i-button type="default" :disabled="boo_update1" @click="auditing_update1" size="small" icon="ios-brush-outline"> Update</i-button>
				</i-col>
			</i-row>
			
			<br><br>

			<i-row :gutter="16">
				<i-col span="5">
					<Tree ref="tree_applicant" :data="treedata_applicant" :load-data="loadTreeData" @on-select-change="onselectchange_user_current"></Tree>
				</i-col>
				<i-col span="6">
					<Tree ref="tree_auditing" :data="treedata_auditing" :load-data="loadTreeData" show-checkbox></Tree>
				</i-col>
				<i-col span="13">
				<i-table height="300" size="small" border :columns="tablecolumns_auditing1" :data="tabledata_auditing1"></i-table>
				</i-col>

			</i-row>

			&nbsp;

			<br><br>

		</Tab-pane>

		<Tab-pane label="单独指定处理用户（申请）">
			<i-row :gutter="16">
				<i-col span="24">
					<font color="#ff9900">* 在此指定哪些用户可以处理“当前用户”提交的申请。</font>
					&nbsp;
				</i-col>
			</i-row>
			
			<br><br>

			<i-row :gutter="16">
				<i-col span="15">
					当前用户工号：&nbsp;
					<i-select v-model.lazy="user_select_current_applicant" filterable remote :remote-method="remoteMethod_user_current_applicant" :loading="user_loading_current_applicant" @on-change="onchange_user_current_applicant" clearable placeholder="输入工号后选择" style="width: 120px;" size="small">
						<i-option v-for="item in user_options_current_applicant" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
					</i-select>
					&nbsp;&nbsp;当前用户姓名：&nbsp;@{{ username_current2_applicant }}&nbsp;
				</i-col>
				<i-col span="9">
					&nbsp;
				</i-col>
			</i-row>
			
			<br><br>

			<i-row :gutter="16">
				<i-col span="15">
					处理用户工号：&nbsp;
					<i-select v-model.lazy="user_select_auditing_applicant" filterable remote :remote-method="remoteMethod_user_auditing_applicant" :loading="user_loading_auditing_applicant" @on-change="onchange_user_auditing_applicant" clearable placeholder="输入工号后选择" style="width: 120px;" size="small">
						<i-option v-for="item in user_options_auditing_applicant" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
					</i-select>
					&nbsp;&nbsp;处理用户姓名：&nbsp;@{{ username_auditing2_applicant }}&nbsp;
				</i-col>
				<i-col span="9">
					&nbsp;
				</i-col>
			</i-row>
			
			<br><br>

			<i-row :gutter="16">
				<i-col span="24">
					<i-button type="default" :disabled="boo_update2_applicant" @click="auditing_add2_applicant" size="small" icon="ios-add"> Add</i-button>
				</i-col>
			</i-row>

			<br><br>

			<i-table height="300" size="small" border :columns="tablecolumns_auditing2_applicant" :data="tabledata_auditing2_applicant"></i-table>

			<br><br>

		</Tab-pane>
		</Tabs>
	</Tab-pane>

	<Tab-pane label="确认流程">
		<Tabs type="card" v-model="currentsubtabs2" :animated="false">

		<Tab-pane label="批量指定处理用户（确认）">
			<i-row :gutter="16">
				<i-col span="24">
					<font color="#ff9900">* 在此指定哪些用户可以处理“当前用户”提交的申请。</font>
					&nbsp;
				</i-col>
			</i-row>
			
			<br><br>
			<i-row :gutter="16">
				<i-col span="5">
					当前代理申请用户：&nbsp;<strong>@{{ username_current1 }}</strong>
					
				</i-col>
				<i-col span="19">
				&nbsp;&nbsp;<i-button type="default" :disabled="boo_update1" @click="auditing_update1" size="small" icon="ios-brush-outline"> Update</i-button>
				</i-col>
			</i-row>
			
			<br><br>

			<i-row :gutter="16">
				<i-col span="5">
					<Tree ref="tree_applicant" :data="treedata_applicant" :load-data="loadTreeData" @on-select-change="onselectchange_user_current"></Tree>
				</i-col>
				<i-col span="6">
					<Tree ref="tree_auditing" :data="treedata_auditing" :load-data="loadTreeData" show-checkbox></Tree>
				</i-col>
				<i-col span="13">
				<i-table height="300" size="small" border :columns="tablecolumns_auditing1" :data="tabledata_auditing1"></i-table>
				</i-col>

			</i-row>

			&nbsp;

			<br><br>

		</Tab-pane>

		<Tab-pane label="单独指定处理用户（确认）">
			<i-row :gutter="16">
				<i-col span="24">
					<font color="#ff9900">* 在此指定哪些用户可以处理“当前用户”提交的确认。</font>
					&nbsp;
				</i-col>
			</i-row>
			
			<br><br>

			<i-row :gutter="16">
				<i-col span="15">
					当前用户工号：&nbsp;
					<i-select v-model.lazy="user_select_current_confirm" filterable remote :remote-method="remoteMethod_user_current_confirm" :loading="user_loading_current_confirm" @on-change="onchange_user_current_confirm" clearable placeholder="输入工号后选择" style="width: 120px;" size="small">
						<i-option v-for="item in user_options_current_confirm" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
					</i-select>
					&nbsp;&nbsp;当前用户姓名：&nbsp;@{{ username_current2_confirm }}&nbsp;
				</i-col>
				<i-col span="9">
					&nbsp;
				</i-col>
			</i-row>
			
			<br><br>

			<i-row :gutter="16">
				<i-col span="15">
					处理用户工号：&nbsp;
					<i-select v-model.lazy="user_select_auditing_confirm" filterable remote :remote-method="remoteMethod_user_auditing_confirm" :loading="user_loading_auditing_confirm" @on-change="onchange_user_auditing_confirm" clearable placeholder="输入工号后选择" style="width: 120px;" size="small">
						<i-option v-for="item in user_options_auditing_confirm" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
					</i-select>
					&nbsp;&nbsp;处理用户姓名：&nbsp;@{{ username_auditing2_confirm }}&nbsp;
				</i-col>
				<i-col span="9">
					&nbsp;
				</i-col>
			</i-row>
			
			<br><br>

			<i-row :gutter="16">
				<i-col span="24">
					<i-button type="default" :disabled="boo_update2_confirm" @click="auditing_add2_confirm" size="small" icon="ios-add"> Add</i-button>
				</i-col>
			</i-row>

			<br><br>

			<i-table height="300" size="small" border :columns="tablecolumns_auditing2_confirm" :data="tabledata_auditing2_confirm"></i-table>

			<br><br>

		</Tab-pane>
		</Tabs>
	</Tab-pane>

	<Tab-pane label="导入用户">
		<i-row :gutter="16">
			<i-col span="24">
				<font color="#ff9900">* 在此导入外部数据源用户。</font>
			</i-col>
		</i-row>

		<br><br>

		<i-row :gutter="16">
			<i-col span="24">
				<i-button type="default" @click="getExternalUsers" size="small" icon="ios-refresh"> Refresh</i-button>
			</i-col>
		</i-row>

		<br><br>

		<i-row :gutter="16">
			<i-col span="24">
				@{{ users_external }}
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
		
		sideractivename: '3-1',
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
			// {
			// 	title: 'id',
			// 	key: 'id',
			// 	sortable: true,
			// 	width: 80
			// },
			{
				title: 'uid',
				key: 'uid',
				sortable: true,
				width: 100
			},
			{
				title: 'name',
				key: 'name',
				width: 100
			},
			{
				title: 'department',
				key: 'department',
				width: 130
			},
			// {
			// 	title: 'ldapname',
			// 	key: 'ldapname',
			// 	width: 130
			// },
			// {
			// 	title: 'email',
			// 	key: 'email',
			// 	width: 240
			// },
			// {
			// 	title: 'displayname',
			// 	key: 'displayname',
			// 	width: 180
			// },
			{
				title: 'login IP',
				key: 'login_ip',
				width: 130
			},
			{
				title: 'counts',
				key: 'login_counts',
				align: 'center',
				sortable: true,
				width: 100
			},
			{
				title: 'login time',
				key: 'login_time',
				width: 160
			},
			{
				title: 'status',
				key: 'deleted_at',
				align: 'center',
				width: 80,
				render: (h, params) => {
					return h('div', [
						// params.row.deleted_at.toLocaleString()
						// params.row.deleted_at ? '禁用' : '启用'
						
						h('i-switch', {
							props: {
								type: 'primary',
								size: 'small',
								value: ! params.row.deleted_at
							},
							style: {
								marginRight: '5px'
							},
							on: {
								'on-change': (value) => {//触发事件是on-change,用双引号括起来，
									//参数value是回调值，并没有使用到
									vm_app.trash_user(params.row.id) //params.index是拿到table的行序列，可以取到对应的表格值
								}
							}
						}, 'Edit')
						
					]);
				}
			},
			{
				title: 'created_at',
				key: 'created_at',
				width: 160
			},
			{
				title: 'Action',
				key: 'action',
				align: 'center',
				width: 140,
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
									vm_app.user_edit(params.row)
								}
							}
						}, 'Edit'),
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
									vm_app.user_clsttl(params.row)
								}
							}
						}, 'ClsTTL')
					]);
				},
				fixed: 'right'
			}
		],
		tabledata: [],
		tableselect: [],

		tablecolumns_auditing1: [
			{
				type: 'index',
				align: 'center',
				width: 60,
			},
			{
				title: 'uid',
				key: 'uid',
				width: 100
			},
			{
				title: 'name',
				key: 'name',
				width: 100
			},
			{
				title: 'department',
				key: 'department',
				width: 130
			},
			{
				title: 'status',
				key: 'deleted_at',
				align: 'center',
				width: 80,
				render: (h, params) => {
					return h('div', [
						// params.row.deleted_at.toLocaleString()
						// params.row.deleted_at ? '禁用' : '启用'
						
						h('i-switch', {
							props: {
								type: 'primary',
								size: 'small',
								value: ! params.row.deleted_at
							},
							style: {
								marginRight: '5px'
							},
							on: {
								'on-change': (value) => {//触发事件是on-change,用双引号括起来，
									//参数value是回调值，并没有使用到
									vm_app.trash_user(params.row.id) //params.index是拿到table的行序列，可以取到对应的表格值
								}
							}
						}, 'Edit')
						
					]);
				}
			},
			{
				title: 'Action',
				key: 'action',
				align: 'center',
				width: 140,
				render: (h, params) => {
					return h('div', [
						h('Button', {
							props: {
								type: 'default',
								size: 'small',
								icon: 'md-arrow-round-down'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.auditing_down1(params)
								}
							}
						}),
						h('Button', {
							props: {
								type: 'default',
								size: 'small',
								icon: 'md-arrow-round-up'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.auditing_up1(params)
								}
							}
						}),
						h('Button', {
							props: {
								type: 'default',
								size: 'small',
								icon: 'md-close'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.auditing_remove1(params.row)
								}
							}
						}),
					]);
				},
				// fixed: 'right'
			}
		],
		tabledata_auditing1: [],
		tableselect_auditing1: [],


		tablecolumns_auditing2_applicant: [
			{
				type: 'index',
				align: 'center',
				width: 60,
			},
			{
				title: 'uid',
				key: 'uid',
				width: 100
			},
			{
				title: 'name',
				key: 'name',
				width: 100
			},
			{
				title: 'department',
				key: 'department',
				width: 130
			},
			{
				title: 'status',
				key: 'deleted_at',
				align: 'center',
				width: 80,
				render: (h, params) => {
					return h('div', [
						// params.row.deleted_at.toLocaleString()
						// params.row.deleted_at ? '禁用' : '启用'
						
						h('i-switch', {
							props: {
								type: 'primary',
								size: 'small',
								value: ! params.row.deleted_at
							},
							style: {
								marginRight: '5px'
							},
							on: {
								'on-change': (value) => {//触发事件是on-change,用双引号括起来，
									//参数value是回调值，并没有使用到
									vm_app.trash_user(params.row.id) //params.index是拿到table的行序列，可以取到对应的表格值
								}
							}
						}, 'Edit')
						
					]);
				}
			},
			{
				title: 'Action',
				key: 'action',
				align: 'center',
				width: 140,
				render: (h, params) => {
					return h('div', [
						h('Button', {
							props: {
								type: 'default',
								size: 'small',
								icon: 'md-arrow-round-down'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.auditing_down2(params)
								}
							}
						}),
						h('Button', {
							props: {
								type: 'default',
								size: 'small',
								icon: 'md-arrow-round-up'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.auditing_up2(params)
								}
							}
						}),
						h('Button', {
							props: {
								type: 'default',
								size: 'small',
								icon: 'md-close'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.auditing_remove2(params.row)
								}
							}
						}),
					]);
				},
				// fixed: 'right'
			}
		],
		tabledata_auditing2_applicant: [],
		tableselect_auditing2_applicant: [],

		tablecolumns_auditing2_confirm: [
			{
				type: 'index',
				align: 'center',
				width: 60,
			},
			{
				title: 'uid',
				key: 'uid',
				width: 100
			},
			{
				title: 'name',
				key: 'name',
				width: 100
			},
			{
				title: 'department',
				key: 'department',
				width: 130
			},
			{
				title: 'status',
				key: 'deleted_at',
				align: 'center',
				width: 80,
				render: (h, params) => {
					return h('div', [
						// params.row.deleted_at.toLocaleString()
						// params.row.deleted_at ? '禁用' : '启用'
						
						h('i-switch', {
							props: {
								type: 'primary',
								size: 'small',
								value: ! params.row.deleted_at
							},
							style: {
								marginRight: '5px'
							},
							on: {
								'on-change': (value) => {//触发事件是on-change,用双引号括起来，
									//参数value是回调值，并没有使用到
									vm_app.trash_user(params.row.id) //params.index是拿到table的行序列，可以取到对应的表格值
								}
							}
						}, 'Edit')
						
					]);
				}
			},
			{
				title: 'Action',
				key: 'action',
				align: 'center',
				width: 140,
				render: (h, params) => {
					return h('div', [
						h('Button', {
							props: {
								type: 'default',
								size: 'small',
								icon: 'md-arrow-round-down'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.auditing_down2_confirm(params)
								}
							}
						}),
						h('Button', {
							props: {
								type: 'default',
								size: 'small',
								icon: 'md-arrow-round-up'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.auditing_up2_confirm(params)
								}
							}
						}),
						h('Button', {
							props: {
								type: 'default',
								size: 'small',
								icon: 'md-close'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.auditing_remove2_confirm(params.row)
								}
							}
						}),
					]);
				},
				// fixed: 'right'
			}
		],
		tabledata_auditing2_confirm: [],
		tableselect_auditing2_confirm: [],
		
		//分页
		page_current: 1,
		page_total: 1, // 记录总数，非总页数
		page_size: {{ $config['PERPAGE_RECORDS_FOR_USER'] }},
		page_last: 1,		
		
		// 创建
		modal_user_add: false,
		user_add_id: '',
		user_add_name: '',
		user_add_displayname: '',
		user_add_email: '',
		user_add_department: '',
		user_add_uid: '',
		user_add_password: '',
		
		// 编辑
		modal_user_edit: false,
		user_edit_id: '',
		user_edit_name: '',
		user_edit_email: '',
		user_edit_displayname: '',
		user_edit_department: '',
		user_edit_uid: '',
		user_edit_password: '',
		
		// 删除
		delete_disabled_user: true,

		// tabs索引
		currenttabs: 0,
		currentsubtabs1: 0,
		currentsubtabs2: 0,
		
		// 查询过滤器
		queryfilter_name: "{{ $config['FILTERS_USER_NAME'] }}",
		queryfilter_email: "{{ $config['FILTERS_USER_EMAIL'] }}",
		queryfilter_logintime: "{{ $config['FILTERS_USER_LOGINTIME'] }}" || [],
		queryfilter_loginip: "{{ $config['FILTERS_USER_LOGINIP'] }}",
		
		// 查询过滤器下拉
		collapse_query: '',
		
		// 选择用户查看编辑相应角色 申请
		user_select_current_applicant: '',
		user_options_current_applicant: [],
		user_loading_current_applicant: false,
		user_select_auditing_applicant: '',
		user_select_auditing_uid: '',
		user_options_auditing_applicant: [],
		user_loading_auditing_applicant: false,
		boo_update2_applicant: true,
		username_current2_applicant: '',
		username_auditing2_applicant: '',
		
		// 选择用户查看编辑相应角色 确认
		user_select_current_confirm: '',
		user_options_current_confirm: [],
		user_loading_current_confirm: false,
		user_select_auditing_confirm: '',
		// user_select_auditing_uid: '',
		user_options_auditing_confirm: [],
		user_loading_auditing_confirm: false,
		boo_update2_confirm: true,
		username_current2_confirm: '',
		username_auditing2_confirm: '',

		boo_update1: true,
		username_current1: '',
		username_auditing1: '',
		// 代理用户
		treedata_applicant: [
			{
				title: '请选择代理申请用户',
				loading: false,
				children: []
			}
		],

		// 处理用户
		treedata_auditing: [
			{
				title: '请选择处理用户',
				loading: false,
				children: []
			}
		],

		// 外部数据源用户
		users_external: '',
		
		
		
		
		
		
		
		
		
		
		
		
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


		// 切换当前页
		oncurrentpagechange: function (currentpage) {
			this.usergets(currentpage, this.page_last);
		},
		// 切换页记录数
		onpagesizechange: function (pagesize) {
			
			var _this = this;
			var cfg_data = {};
			cfg_data['PERPAGE_RECORDS_FOR_USER'] = pagesize;
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
					_this.usergets(1, _this.page_last);
				} else {
					_this.warning(false, 'Warning', 'failed!');
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', 'failed!');
			})
		},
		
		usergets: function(page, last_page){
			var _this = this;
			
			if (page > last_page) {
				page = last_page;
			} else if (page < 1) {
				page = 1;
			}
			
			var queryfilter_logintime = [];

			for (var i in _this.queryfilter_logintime) {
				if (typeof(_this.queryfilter_logintime[i])!='string') {
					queryfilter_logintime.push(_this.queryfilter_logintime[i].Format("yyyy-MM-dd"));
				} else if (_this.queryfilter_logintime[i] == '') {
					// queryfilter_logintime.push(new Date().Format("yyyy-MM-dd"));
					// _this.tabledata = [];
					// return false;
					queryfilter_logintime = ['1970-01-01', '9999-12-31'];
					break;
				} else {
					queryfilter_logintime.push(_this.queryfilter_logintime[i]);
				}
			}
			// console.log(queryfilter_logintime);

			var queryfilter_name = _this.queryfilter_name;
			var queryfilter_email = _this.queryfilter_email;
			var queryfilter_displayname = _this.queryfilter_displayname;
			var queryfilter_loginip = _this.queryfilter_loginip;

			_this.loadingbarstart();
			var url = "{{ route('admin.user.list') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					perPage: _this.page_size,
					page: page,
					queryfilter_name: queryfilter_name,
					queryfilter_logintime: queryfilter_logintime,
					queryfilter_email: queryfilter_email,
					queryfilter_displayname: queryfilter_displayname,
					queryfilter_loginip: queryfilter_loginip,
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
					_this.delete_disabled_user = true;
					_this.tableselect = [];

					_this.page_current = response.data.current_page;
					_this.page_total = response.data.total;
					_this.page_last = response.data.last_page;
					_this.tabledata = response.data.data;
					// console.log(_this.tabledata);
				}
				
				_this.loadingbarfinish();
			})
			.catch(function (error) {
				_this.loadingbarerror();
				_this.error(false, 'Error', error);
			})
		},		
		
		// 表user选择
		onselectchange: function (selection) {
			var _this = this;
			_this.tableselect = [];

			for (var i in selection) {
				_this.tableselect.push(selection[i].id);
			}
			
			_this.delete_disabled_user = _this.tableselect[0] == undefined ? true : false;
		},
		
		// user编辑前查看
		user_edit: function (row) {
			var _this = this;
			
			_this.user_edit_id = row.id;
			_this.user_edit_name = row.name;
			_this.user_edit_email = row.email;
			_this.user_edit_displayname = row.displayname;
			_this.user_edit_department = row.department;
			_this.user_edit_uid = row.uid;
			// _this.user_edit_password = row.password;
			// _this.relation_xuqiushuliang_edit[0] = row.xuqiushuliang;
			// _this.relation_xuqiushuliang_edit[1] = row.xuqiushuliang;
			// _this.user_created_at_edit = row.created_at;
			// _this.user_updated_at_edit = row.updated_at;

			_this.modal_user_edit = true;
		},		
		

		// user编辑后保存
		user_edit_ok: function () {
			var _this = this;
			
			var id = _this.user_edit_id;
			var name = _this.user_edit_name;
			var email = _this.user_edit_email;
			var displayname = _this.user_edit_displayname;
			var department = _this.user_edit_department;
			var uid = _this.user_edit_uid;
			var password = _this.user_edit_password;
			// var created_at = _this.relation_created_at_edit;
			// var updated_at = _this.relation_updated_at_edit;
			
			if (name == '' || name == null || name == undefined
				// || ldapname == '' || ldapname == null || ldapname == undefined
				|| email == '' || email == null || email == undefined
				|| displayname == '' || displayname == null || displayname == undefined
				|| department == '' || department == null || department == undefined
				|| uid == '' || uid == null || uid == undefined) {
				_this.warning(false, '警告', '内容不能为空！');
				return false;
			}
			
			var regexp = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/;
			if (! regexp.test(email)) {
				_this.warning(false, '错误', '邮箱地址不正确！');
				return false;
			}
			
			var url = "{{ route('admin.user.update') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				id: id,
				name: name,
				email: email,
				displayname: displayname,
				department: department,
				uid: uid,
				password: password,
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
				
				_this.usergets(_this.page_current, _this.page_last);
				
				if (response.data) {
					_this.success(false, '成功', '更新成功！');
					
					_this.user_edit_id = '';
					_this.user_edit_name = '';
					_this.user_edit_displayname = '';
					_this.user_edit_department = '';
					_this.user_edit_uid = '';
					_this.user_edit_password = '';
					
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
		
		// ondelete_user
		ondelete_user: function () {
			var _this = this;
			
			var tableselect = _this.tableselect;
			
			if (tableselect[0] == undefined) return false;
			
			var url = "{{ route('admin.user.delete') }}";
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
					_this.usergets(_this.page_current, _this.page_last);
					_this.success(false, '成功', '删除成功！');
				} else {
					_this.error(false, '失败', '删除失败！请确认用户与角色或权限的关系！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '删除失败！请确认用户与角色或权限的关系！');
			})
		},
		
		trash_user: function (userid) {
			var _this = this;
			
			if (userid == undefined || userid.length == 0) return false;
			var url = "{{ route('admin.user.trash') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				userid: userid
			})
			.then(function (response) {
				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
				if (response.data) {
					_this.success(false, '成功', 'User 禁用/启用 successfully!');
					_this.usergets(_this.page_current, _this.page_last);
				} else {
					_this.error(false, '失败', '禁用/启用失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '禁用/启用失败！');
			})			
		},
		
		// 显示新建用户
		oncreate_user: function () {
			// 默认密码为12345678
			this.user_add_password = '12345678';
			this.modal_user_add = true;
		},
		
		// 新建用户
		oncreate_user_ok: function () {
			var _this = this;
			var name = _this.user_add_name;
			// var ldapname = _this.user_add_ldapname;
			var department = _this.user_add_department;
			var uid = _this.user_add_uid;
			var email = _this.user_add_email;
			var displayname = _this.user_add_displayname;
			var password = _this.user_add_password;
			
			if (name == '' || name == null || name == undefined
				|| email == '' || email == null || email == undefined
				|| displayname == '' || displayname == null || displayname == undefined
				|| department == '' || department == null || department == undefined
				|| uid == '' || uid == null || uid == undefined
				|| password == '' || password == null || password == undefined) {
				_this.warning(false, '警告', '内容不能为空！');
				return false;
			}
			
			// var re = new RegExp(“a”);  //RegExp对象。参数就是我们想要制定的规则。有一种情况必须用这种方式，下面会提到。
			// var re = /a/;   // 简写方法 推荐使用 性能更好  不能为空 不然以为是注释 ，
			var regexp = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/;
			if (! regexp.test(email)) {
				_this.warning(false, '警告', '邮箱地址错误！');
				return false;
			}

			var url = "{{ route('admin.user.create') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				name: name,
				department: department,
				uid: uid,
				email: email,
				displayname: displayname,
				password: password
			})
			.then(function (response) {
				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
				if (response.data) {
					_this.success(false, '成功', '用户创建成功！');
					_this.user_add_name = '';
					// _this.user_add_ldapname = '';
					_this.user_add_department = '';
					_this.user_add_uid = '';
					_this.user_add_email = '';
					_this.user_add_displayname = '';
					_this.user_add_password = '';
					_this.usergets(_this.page_current, _this.page_last);
				} else {
					_this.error(false, '失败', '用户创建失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '用户创建失败！');
			})
		},		
		
		// 导出用户
		onexport_user: function(){
			var url = "{{ route('admin.user.excelexport') }}";
			window.setTimeout(function(){
				window.location.href = url;
			}, 1000);
			return false;
		},
		
		// ClearTTL
		user_clsttl: function (row) {
			var _this = this;
			var id = row.id;

			var url = "{{ route('admin.user.clsttl') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				id: id,
			})
			.then(function (response) {
				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
 				if (response.data) {
					_this.success(false, '成功', '清除用户登录TTL成功！');
				} else {
					_this.error(false, '失败', '清除用户登录TTL失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '清除用户登录TTL失败！');
			})
			
		},

		// auditing_update1
		auditing_update1 () {
			var _this = this;
			var json_applicant = _this.$refs.tree_applicant.getSelectedNodes();
			var json_auditing = _this.$refs.tree_auditing.getCheckedNodes();

			if (json_applicant == '' || json_auditing == ''
				|| json_applicant == undefined || json_auditing == undefined) {
				_this.warning(false, '警告', '内容不能为空！');
				return false;
			}

			var applicant = '';
			let tmp = json_applicant[0]['title'].split(' (ID:');
			if (tmp[1]) {
				applicant = tmp[1].substr(0, tmp[1].length-1);
			} else {
				_this.warning(false, '警告', '代理申请人选择错误！');
				return false;
			}

			var auditings = [];
			var str = '';
			for (var key in json_auditing) {
				// 截取字符
				let tmp = json_auditing[key]['title'].split(' (ID:');
				if (tmp[1]) {
					str = tmp[1].substr(0, tmp[1].length-1);
					auditings.push(str);
				}
			}

			var url = "{{ route('admin.user.auditingupdate') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				applicant: applicant,
				auditings: auditings,
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
 				if (response.data) {
					_this.success(false, '成功', '更新处理用户成功！');
					_this.tabledata_auditing1 = response.data;
				} else {
					_this.error(false, '失败', '更新处理用户失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '更新处理用户失败！');
			})
			
		},

		// 添加处理用户 申请
		auditing_add2_applicant () {
			var _this = this;

			var id_current = _this.user_select_current_applicant;
			var id_auditing = _this.user_select_auditing_applicant;

			if (id_current == '' || id_auditing == ''
				|| id_current == undefined || id_auditing == undefined) {
					_this.error(false, '失败', '用户ID为空或不正确！');
					return false;
			}

			// if (id_current == id_auditing) {
			// 		_this.error(false, '失败', '自己不能处理自己的申请！');
			// 		return false;
			// }

			// console.log(_this.user_select_current_applicant);
			// return false;

			var url = "{{ route('admin.user.auditingadd') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				id_current: id_current,
				id_auditing: id_auditing,
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
 				if (response.data) {
					_this.success(false, '成功', '添加处理用户成功！');
					_this.tabledata_auditing2_applicant = response.data;
				} else {
					_this.error(false, '失败', '添加处理用户失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '添加处理用户失败！');
			})
			
		},

		// 添加处理用户 确认
		auditing_add2_confirm () {
			var _this = this;

			var id_current = _this.user_select_current_confirm;
			var id_auditing = _this.user_select_auditing_confirm;

			if (id_current == '' || id_auditing == ''
				|| id_current == undefined || id_auditing == undefined) {
					_this.error(false, '失败', '用户ID为空或不正确！');
					return false;
			}

			var url = "{{ route('admin.user.auditingaddconfirm') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				id_current: id_current,
				id_auditing: id_auditing,
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
 				if (response.data) {
					_this.success(false, '成功', '添加处理用户成功！');
					_this.tabledata_auditing2_confirm = response.data;
				} else {
					_this.error(false, '失败', '添加处理用户失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '添加处理用户失败！');
			})
			
		},

				
		// sort向前
		auditing_up1 (params) {
			var _this = this;
			var index = params.row._index;
			var uid = params.row.uid;

			// current user id -> id
			var json_applicant = _this.$refs.tree_applicant.getSelectedNodes();

			if (json_applicant == ''
				|| json_applicant == undefined) {
				_this.warning(false, '警告', '内容不能为空！');
				return false;
			}

			let tmp = json_applicant[0]['title'].split(' (ID:');
			if (tmp[1]) {
				var id = tmp[1].substr(0, tmp[1].length-1);
			} else {
				_this.warning(false, '警告', '代理申请人选择错误！');
				return false;
			}

			if (id == '' || uid == ''
				|| id == undefined || uid == undefined || index == undefined
				|| id == uid || index == 0) {
				return false;
			}

			var url = "{{ route('admin.user.auditingsort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				index: index,
				uid: uid,
				id: id,
				sort: 'up'
			})
			.then(function (response) {
				// console.log(response.data);return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}

				if (response.data) {
					_this.success(false, '成功', '排序成功！');
					_this.tabledata_auditing1 = response.data;
				} else {
					_this.error(false, '失败', '排序失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '排序失败！');
			})
		},
		
		
		// sort向后
		auditing_down1 (params) {
			var _this = this;
			var index = params.row._index;
			var uid = params.row.uid;

			// current user id -> id
			var json_applicant = _this.$refs.tree_applicant.getSelectedNodes();

			if (json_applicant == ''
				|| json_applicant == undefined) {
				_this.warning(false, '警告', '内容不能为空！');
				return false;
			}

			let tmp = json_applicant[0]['title'].split(' (ID:');
			if (tmp[1]) {
				var id = tmp[1].substr(0, tmp[1].length-1);
			} else {
				_this.warning(false, '警告', '代理申请人选择错误！');
				return false;
			}

			if (id == '' || uid == ''
				|| id == undefined || uid == undefined || index == undefined
				|| id == uid || index == _this.tabledata_auditing1.length-1) {
				return false;
			}

			var url = "{{ route('admin.user.auditingsort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				index: index,
				uid: uid,
				id: id,
				sort: 'down'
			})
			.then(function (response) {
				// console.log(response.data);return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}

				if (response.data) {
					_this.success(false, '成功', '排序成功！');
					_this.tabledata_auditing1 = response.data;
				} else {
					_this.error(false, '失败', '排序失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '排序失败！');
			})
		},


		// auditing_remove1
		auditing_remove1 (row) {
			var _this = this;
			// console.log(row._index);
			// return false;

			var index = row._index;
			var uid = row.uid;

			var json_applicant = _this.$refs.tree_applicant.getSelectedNodes();

			if (json_applicant == ''
				|| json_applicant == undefined) {
				_this.warning(false, '警告', '内容不能为空！');
				return false;
			}

			let tmp = json_applicant[0]['title'].split(' (ID:');
			if (tmp[1]) {
				var id = tmp[1].substr(0, tmp[1].length-1);
			} else {
				_this.warning(false, '警告', '代理申请人选择错误！');
				return false;
			}

			if (id == '' || uid == ''
				|| id == undefined || uid == undefined || index == undefined
				|| id == uid) {
				return false;
			}

			// console.log(_this.user_select_current_applicant);
			// return false;

			var url = "{{ route('admin.user.auditingremove') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				index: index,
				id: id,
				uid: uid,
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
 				if (response.data) {
					_this.success(false, '成功', '删除处理用户成功！');
					_this.tabledata_auditing1 = response.data;
				} else {
					_this.error(false, '失败', '删除处理用户失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '删除处理用户失败！');
			})
			
		},


		// sort向前
		auditing_up2 (params) {
			var _this = this;
			var index = params.row._index;
			var uid = params.row.uid;

			// current user id -> id
			var id = _this.user_select_current_applicant;

			if (id == '' || uid == ''
				|| id == undefined || uid == undefined || index == undefined
				|| id == uid || index == 0) {
				return false;
			}

			var url = "{{ route('admin.user.auditingsort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				index: index,
				uid: uid,
				id: id,
				sort: 'up'
			})
			.then(function (response) {
				// console.log(response.data);return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}

				if (response.data) {
					_this.success(false, '成功', '排序成功！');
					_this.tabledata_auditing2_applicant = response.data;
				} else {
					_this.error(false, '失败', '排序失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '排序失败！');
			})
		},

		// sort向前
		auditing_up2_confirm (params) {
			var _this = this;
			var index = params.row._index;
			var uid = params.row.uid;

			// current user id -> id
			var id = _this.user_select_current_confirm;

			if (id == '' || uid == ''
				|| id == undefined || uid == undefined || index == undefined
				|| id == uid || index == 0) {
				return false;
			}

			var url = "{{ route('admin.user.auditingsortconfirm') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				index: index,
				uid: uid,
				id: id,
				sort: 'up'
			})
			.then(function (response) {
				// console.log(response.data);return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}

				if (response.data) {
					_this.success(false, '成功', '排序成功！');
					_this.tabledata_auditing2_confirm = response.data;
				} else {
					_this.error(false, '失败', '排序失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '排序失败！');
			})
		},
		
		
		// sort向后
		auditing_down2 (params) {
			var _this = this;
			var index = params.row._index;
			var uid = params.row.uid;

			// current user id -> id
			var id = _this.user_select_current_applicant;

			if (id == '' || uid == ''
				|| id == undefined || uid == undefined || index == undefined
				|| id == uid || index == _this.tabledata_auditing2_applicant.length-1) {
				return false;
			}

			var url = "{{ route('admin.user.auditingsort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				index: index,
				uid: uid,
				id: id,
				sort: 'down'
			})
			.then(function (response) {
				// console.log(response.data);return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}

				if (response.data) {
					_this.success(false, '成功', '排序成功！');
					_this.tabledata_auditing2_applicant = response.data;
				} else {
					_this.error(false, '失败', '排序失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '排序失败！');
			})
		},
		
		// sort向后
		auditing_down2_confirm (params) {
			var _this = this;
			var index = params.row._index;
			var uid = params.row.uid;

			// current user id -> id
			var id = _this.user_select_current_confirm;

			if (id == '' || uid == ''
				|| id == undefined || uid == undefined || index == undefined
				|| id == uid || index == _this.tabledata_auditing2_confirm.length-1) {
				return false;
			}

			var url = "{{ route('admin.user.auditingsortconfirm') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				index: index,
				uid: uid,
				id: id,
				sort: 'down'
			})
			.then(function (response) {
				// console.log(response.data);return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}

				if (response.data) {
					_this.success(false, '成功', '排序成功！');
					_this.tabledata_auditing2_confirm = response.data;
				} else {
					_this.error(false, '失败', '排序失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '排序失败！');
			})
		},


		// auditing_remove2
		auditing_remove2 (row) {
			var _this = this;
			// console.log(row._index);
			// return false;

			var index = row._index;
			var uid = row.uid;
			var id = _this.user_select_current_applicant;

			if (id == '' || uid == ''
				|| id == undefined || uid == undefined
				|| id == uid) {
				return false;
			}

			// console.log(_this.user_select_current_applicant);
			// return false;

			var url = "{{ route('admin.user.auditingremove') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				index: index,
				id: id,
				uid: uid,
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
 				if (response.data) {
					_this.success(false, '成功', '删除处理用户成功！');
					_this.tabledata_auditing2_applicant = response.data;
				} else {
					_this.error(false, '失败', '删除处理用户失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '删除处理用户失败！');
			})
			
		},

		// auditing_remove2_confirm
		auditing_remove2_confirm (row) {
			var _this = this;
			// console.log(row._index);
			// return false;

			var index = row._index;
			var uid = row.uid;
			var id = _this.user_select_current_confirm;

			if (id == '' || uid == ''
				|| id == undefined || uid == undefined
				|| id == uid) {
				return false;
			}

			// console.log(_this.user_select_current_applicant);
			// return false;

			var url = "{{ route('admin.user.auditingremoveconfirm') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				index: index,
				id: id,
				uid: uid,
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
 				if (response.data) {
					_this.success(false, '成功', '删除处理用户成功！');
					_this.tabledata_auditing2_confirm = response.data;
				} else {
					_this.error(false, '失败', '删除处理用户失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '删除处理用户失败！');
			})
			
		},
		
		
		// 列出申请单独当前用户信息 OK
		onchange_user_current_applicant() {
			var _this = this;
			var userid = _this.user_select_current_applicant;
			// console.log(userid);return false;
			
			if (userid == undefined || userid == '') {
				_this.tabledata_auditing2_applicant = [];
				_this.username_current2_applicant = '';
				return false;
			}
			// _this.boo_update2_applicant = false;
			var url = "{{ route('admin.user.userhasauditing2') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					userid: userid
				}
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
				if (response.data.auditing) {
					_this.tabledata_auditing2_applicant = response.data.auditing;
					_this.username_current2_applicant = response.data.username;
				} else {
					_this.tabledata_auditing2_applicant = [];
					_this.username_current2_applicant = '';
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
			
		},


		// 选择user auditing 申请 2222222222222222
		onchange_user_auditing_applicant () {
			var _this = this;
			var userid = _this.user_select_auditing_applicant;
			
			// console.log(userid);return false;
			
			if (userid == undefined || userid == '') {
				_this.username_auditing2_applicant = '';
				// _this.targetkeystransfer = [];
				// _this.datatransfer = [];
				_this.boo_update2_applicant = true;
				return false;
			}
			_this.boo_update2_applicant = false;
			var url = "{{ route('admin.user.userhasauditing2') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					userid: userid
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
					_this.username_auditing2_applicant = response.data.username;
					_this.user_select_auditing_uid = response.data.uid;
				} else {
					_this.username_auditing2_applicant = '';
					_this.user_select_auditing_uid = '';
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
			
		},

		
		// 列出申请单独当前用户信息 OK
		onchange_user_current_confirm() {
			var _this = this;
			var userid = _this.user_select_current_confirm;
			// console.log(userid);return false;
			
			if (userid == undefined || userid == '') {
				_this.tabledata_auditing2_confirm = [];
				_this.username_current2_confirm = '';
				return false;
			}
			// _this.boo_update2_confirm = false;
			var url = "{{ route('admin.user.userhasauditing2confirm') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					userid: userid
				}
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
				if (response.data.auditing) {
					_this.tabledata_auditing2_confirm = response.data.auditing;
					_this.username_current2_confirm = response.data.username;
				} else {
					_this.tabledata_auditing2_confirm = [];
					_this.username_current2_confirm = '';
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
			
		},

		// 选择user auditing 确认 44444444444
		onchange_user_auditing_confirm() {
			var _this = this;
			var userid = _this.user_select_auditing_confirm;
			
			// console.log(userid);return false;
			
			if (userid == undefined || userid == '') {
				_this.username_auditing2_confirm = '';
				// _this.targetkeystransfer = [];
				// _this.datatransfer = [];
				_this.boo_update2_confirm = true;
				return false;
			}
			_this.boo_update2_confirm = false;
			var url = "{{ route('admin.user.userhasauditing2') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					userid: userid
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
					_this.username_auditing2_confirm = response.data.username;
					_this.user_select_auditing_uid = response.data.uid;
				} else {
					_this.username_auditing2_confirm = '';
					_this.user_select_auditing_uid = '';
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
			
		},


		// 选择 tree user current
		onselectchange_user_current () {
			var _this = this;
			var json_applicant = _this.$refs.tree_applicant.getSelectedNodes();

			if (json_applicant == undefined || json_applicant == '') {
				// _this.warning(false, '警告', '代理申请人选择错误！');
				_this.boo_update1 = true;
				return false;
			}

			var applicant = '';
			let tmp = json_applicant[0]['title'].split(' (ID:');
			if (tmp[1]) {
				_this.username_current1 = tmp[0];
				applicant = tmp[1].substr(0, tmp[1].length-1);
			} else {
				_this.warning(false, '警告', '代理申请人选择错误！');
				_this.boo_update1 = true;
				return false;
			}

			// console.log(json_applicant);return false;
			
			_this.boo_update1 = false;
			var url = "{{ route('admin.user.userhasauditing1applicant') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					applicant: applicant
				}
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
				if (response.data.auditing) {
					_this.tabledata_auditing1 = response.data.auditing;
					// _this.username_current2_applicant = response.data.username;
				} else {
					_this.tabledata_auditing1 = [];
					// _this.username_current2_applicant = '';
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
			
		},
		


		// 远程查询当前用户 申请
		remoteMethod_user_current_applicant (query) {
			var _this = this;

			if (query !== '') {
				_this.user_loading_current_applicant = true;
				
				var queryfilter_name = query;
				
				var url = "{{ route('admin.user.uidlist') }}";
				axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
				axios.get(url,{
					params: {
						queryfilter_name: queryfilter_name
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
						var json = response.data;
						_this.user_options_current_applicant = _this.json2selectvalue(json);
					}
				})
				.catch(function (error) {
				})				
				
				setTimeout(() => {
					_this.user_loading_current_applicant = false;
					// const list = this.list.map(item => {
						// return {
							// value: item,
							// label: item
						// };
					// });
					// this.options1 = list.filter(item => item.label.toLowerCase().indexOf(query.toLowerCase()) > -1);
				}, 200);
			} else {
				_this.user_options_current_applicant = [];
			}
		},

		// 远程查询当前用户 确认
		remoteMethod_user_current_confirm (query) {
			var _this = this;

			if (query !== '') {
				_this.user_loading_current_confirm = true;
				
				var queryfilter_name = query;
				
				var url = "{{ route('admin.user.uidlist') }}";
				axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
				axios.get(url,{
					params: {
						queryfilter_name: queryfilter_name
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
						var json = response.data;
						_this.user_options_current_confirm = _this.json2selectvalue(json);
					}
				})
				.catch(function (error) {
				})				
				
				setTimeout(() => {
					_this.user_loading_current_confirm = false;
				}, 200);
			} else {
				_this.user_options_current_applicant = [];
			}
		},

		// 远程查询处理用户
		remoteMethod_user_auditing_applicant (query) {
			var _this = this;

			if (query !== '') {
				_this.user_loading_current_applicant = true;
				
				var queryfilter_name = query;
				
				var url = "{{ route('admin.user.uidlist') }}";
				axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
				axios.get(url,{
					params: {
						queryfilter_name: queryfilter_name
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
						var json = response.data;
						_this.user_options_auditing_applicant = _this.json2selectvalue(json);
					}
				})
				.catch(function (error) {
				})				
				
				setTimeout(() => {
					_this.user_loading_current_applicant = false;
					// const list = this.list.map(item => {
						// return {
							// value: item,
							// label: item
						// };
					// });
					// this.options1 = list.filter(item => item.label.toLowerCase().indexOf(query.toLowerCase()) > -1);
				}, 200);
			} else {
				_this.user_options_current_applicant = [];
			}
		},

		// 远程查询处理用户
		remoteMethod_user_auditing_confirm (query) {
			var _this = this;

			if (query !== '') {
				_this.user_loading_current_confirm = true;
				
				var queryfilter_name = query;
				
				var url = "{{ route('admin.user.uidlist') }}";
				axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
				axios.get(url,{
					params: {
						queryfilter_name: queryfilter_name
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
						var json = response.data;
						_this.user_options_auditing_confirm = _this.json2selectvalue(json);
					}
				})
				.catch(function (error) {
				})				
				
				setTimeout(() => {
					_this.user_loading_current_confirm = false;
				}, 200);
			} else {
				_this.user_options_current_applicant = [];
			}
		},


		// 加载各部门人员
		loadTreeData (item, callback) {
			var _this = this;

			var node = item.node;
			var title = item.title;

			var url = "{{ route('renshi.jiaban.applicant.loadapplicant') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					node: node,
					title: title
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
					var json = response.data;

					var arr = [];
					setTimeout(() => {
						if (node!='department') {
							for (var key in json) {
								arr.push({
									title: json[key],
									loading: false,
									node: 'department',
									children: []
								});
							}
						} else {
							for (var key in json) {
								arr.push({
									title: json[key],
								});
							}
						}
						callback(arr);
					}, 500);
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})

		},


		// 外部数据源用户信息
		getExternalUsers () {
			var _this = this;


			var url = "{{ route('admin.user.getexternalusers') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {}
			})
			.then(function (response) {
				console.log(response.data);
				return false;

				if (response.data['jwt'] == 'logout') {
					_this.alert_logout();
					return false;
				}
				
				if (response.data) {
					var json = response.data;

					var arr = [];
					setTimeout(() => {
						if (node!='department') {
							for (var key in json) {
								arr.push({
									title: json[key],
									loading: false,
									node: 'department',
									children: []
								});
							}
						} else {
							for (var key in json) {
								arr.push({
									title: json[key],
								});
							}
						}
						callback(arr);
					}, 500);
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})

		},
		
		
		
		
		
		


	},
	mounted: function(){
		var _this = this;
		_this.current_nav = '权限管理';
		_this.current_subnav = '用户';
		// 显示所有user
		_this.usergets(1, 1); // page: 1, last_page: 1
	}
});
</script>
@endsection