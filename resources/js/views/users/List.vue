<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="query.keyword" :placeholder="$t('table.keyword')" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />
      <el-select v-model="query.role" :placeholder="$t('table.role')" clearable style="width: 90px" class="filter-item" @change="handleFilter">
        <el-option v-for="item in roles" :key="item" :label="item | uppercaseFirst" :value="item" />
      </el-select>
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
        {{ $t('table.search') }}
      </el-button>
      <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-plus" @click="handleCreate">
        {{ $t('table.add') }}
      </el-button>
      <el-button v-waves :loading="downloading" class="filter-item" type="primary" icon="el-icon-download" @click="handleDownload">
        {{ $t('table.export') }}
      </el-button>
    </div>

    <el-table v-loading="loading" :data="list" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.index }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Ф.И.О.">
        <template slot-scope="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Логин">
        <template slot-scope="scope">
          <span>{{ scope.row.username }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Роль" width="120">
        <template slot-scope="scope">
          <span>{{ scope.row.roles.join(', ') }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Actions" width="350">
        <template slot-scope="scope">
          <el-button 
              v-if="!scope.row.roles.includes('admin')"
              v-permission="['manage user']" 
              type="primary" 
              icon="el-icon-edit"
              @click="openEditUser(scope.row.id)"
          >
          </el-button>
          <el-button  
            v-if="!scope.row.roles.includes('admin')"
            v-permission="['manage user']"
            type="primary"
            @click="changePasswordUser(scope.row.id)"
          >
            <svg-icon icon-class="password"></svg-icon>
          </el-button>
          <el-button 
            v-if="!scope.row.roles.includes('admin')" 
            v-permission="['manage permission']" 
            type="warning" 
            @click="handleEditPermissions(scope.row.id);"
          >
            <svg-icon icon-class="shield-three"></svg-icon>
          </el-button>
          <el-button 
              v-if="!scope.row.roles.includes('admin')" 
              v-permission="['manage user']" 
              type="danger" 
              icon="el-icon-delete" 
              @click="handleDelete(scope.row.id, scope.row.name);"
          >
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getList" />

    <el-dialog :visible.sync="dialogPermissionVisible" :title="'Разрешения пользователя - ' + currentUser.name">
      <div v-if="currentUser.name" v-loading="dialogPermissionLoading" class="form-container">
        <div class="permissions-container">
          <div class="block">
            <el-form :model="currentUser" label-width="80px" label-position="top">
              <el-form-item label="Доступы к меню">
                <el-tree ref="menuPermissions" :data="normalizedMenuPermissions" :default-checked-keys="permissionKeys(userMenuPermissions)" :props="permissionProps" show-checkbox node-key="id" class="permission-tree" />
              </el-form-item>
            </el-form>
          </div>
          <div class="block">
            <el-form :model="currentUser" label-width="80px" label-position="top">
              <el-form-item label="Разрешения">
                <el-tree ref="otherPermissions" :data="normalizedOtherPermissions" :default-checked-keys="permissionKeys(userOtherPermissions)" :props="permissionProps" show-checkbox node-key="id" class="permission-tree" />
              </el-form-item>
            </el-form>
          </div>
          <div class="clear-left" />
        </div>
        <div style="text-align:right;">
          <el-button type="danger" @click="dialogPermissionVisible=false">
            {{ $t('permission.cancel') }}
          </el-button>
          <el-button type="primary" @click="confirmPermission">
            {{ $t('permission.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>

    <el-dialog title="Создание нового пользователя" :visible.sync="dialogFormVisible" :close-on-click-modal="false">
      <div v-loading="userCreating" class="form-container">
        <el-form ref="userForm" :rules="rules" :model="newUser" label-position="left" label-width="250px">
          <el-form-item label="Роль" prop="role">
            <el-select v-model="newUser.role" class="filter-item" placeholder="Выберите роль пользователя">
              <el-option v-for="item in nonAdminRoles" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </el-form-item>
          <el-form-item label="Ф.И.О" prop="name">
            <el-input v-model="newUser.name" />
          </el-form-item>
          <el-form-item label="Должность" prop="job">
            <el-input v-model="newUser.job" />
          </el-form-item>
          <el-form-item label="Телефон" prop="phone">
            <el-input v-model="newUser.phone" />
          </el-form-item>
          <el-form-item label="Логин" prop="username">
            <el-input v-model="newUser.username" />
          </el-form-item>
          <el-form-item label="Пароль" prop="password">
            <el-input v-model="newUser.password" show-password />
          </el-form-item>
          <el-form-item label="Подтверждение пароля" prop="confirmPassword">
            <el-input v-model="newUser.confirmPassword" show-password />
          </el-form-item>
          <el-form-item label="Доступы к площадкам" prop="places">
            <el-select v-model="newUser.places" multiple>
                <el-option-group 
                 v-for="manager in places"
                 :key="manager.id"
                 :label="manager.title"
                >
                  <el-option-group
                    v-for="rudnik in manager.places"
                    :key="rudnik.id"
                    :label="rudnik.title"
                  >
                    <el-option 
                      v-for="place in rudnik.places"
                      :key="place.id"
                      :value="place.id"
                      :label="place.title"
                    >
                    </el-option>
                  </el-option-group>
                </el-option-group>
            </el-select>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createUser()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog> 
    <el-dialog v-if="editUserId" title="Редактирование пользователя" :visible.sync="dialogEditFormVisible" :close-on-click-modal="false">
      <div class="form-container">
        <el-form ref="userEditForm" :model="editUser" label-position="left" label-width="250px">
          <el-form-item label="Роль" prop="role">
            <el-select v-model="editUser.role" class="filter-item" placeholder="Выберите роль пользователя">
              <el-option v-for="item in nonAdminRoles" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </el-form-item>
          <el-form-item label="Ф.И.О" prop="name">
            <el-input v-model="editUser.name" />
          </el-form-item>
          <el-form-item label="Должность" prop="job">
            <el-input v-model="editUser.job" />
          </el-form-item>
          <el-form-item label="Телефон" prop="phone">
            <el-input v-model="editUser.phone" />
          </el-form-item>
          <el-form-item label="Логин" prop="username">
            <el-input v-model="editUser.username" />
          </el-form-item>
          <el-form-item label="Доступы к площадкам" prop="places">
            <el-select v-model="editUser.places" multiple>
                <el-option-group 
                 v-for="manager in places"
                 :key="manager.id"
                 :label="manager.title"
                >
                  <el-option-group
                    v-for="rudnik in manager.places"
                    :key="rudnik.id"
                    :label="rudnik.title"
                  >
                    <el-option 
                      v-for="place in rudnik.places"
                      :key="place.id"
                      :value="place.id"
                      :label="place.title"
                    >
                    </el-option>
                  </el-option-group>
                </el-option-group>
            </el-select>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogEditFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="updateUser()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>

    <el-dialog v-if="changePasswordId" title="Обновление пароля пользователя" :visible.sync="dialogPasswordFormVisible">
      <div class="form-container">
        <el-form ref="passwordForm" :model="changePassword" label-position="left" label-width="250px">
          <el-form-item label="Новый пароль" prop="password">
            <el-input v-model="changePassword.password" show-password />
          </el-form-item>
          <el-form-item label="Подтверждение нового пароля" prop="confirmPassword">
            <el-input v-model="changePassword.confirmPassword" show-password />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="clearPasswordForm">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="updatePassword()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
 
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import UserResource from '@/api/user';
import Resource from '@/api/resource';
import waves from '@/directive/waves'; // Waves directive
import permission from '@/directive/permission'; // Permission directive
import checkPermission from '@/utils/permission'; // Permission checking

import {fetchPlaces} from "@/api/places";
import {fetchRoles} from "@/api/roles";

const userResource = new UserResource();
const permissionResource = new Resource('permissions');

export default {
  name: 'UserList',
  components: { Pagination },
  directives: { waves, permission },
  data() {
    var validateConfirmPassword = (rule, value, callback) => {
      if (value !== this.newUser.password) {
        callback(new Error('Password is mismatched!'));
      } else {
        callback();
      }
    };

    return {
      list: null,
      total: 0,
      loading: true,
      downloading: false,
      userCreating: false,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
      },
      roles: ['admin', 'manager', 'editor', 'user', 'visitor'],
      
      nonAdminRoles: [],
      places: [],


      newUser: {},
      editUser: {},
      changePassword: {},
      changePasswordId: null,
      editUserId: null,

      
      dialogFormVisible: false,
      dialogPermissionVisible: false,
      dialogPermissionLoading: false,
      
      dialogEditFormVisible: false,
      dialogPasswordFormVisible: false,


      currentUserId: 0,
      currentUser: {
        name: '',
        permissions: [],
        rolePermissions: [],  
      },
      rules: {
        role: [{ required: true, message: 'Роль является обязательным полем', trigger: 'change' }],
        name: [{ required: true, message: 'Ф.И.О является обязательным полем', trigger: 'blur' }],
        username: [
          { required: true, message: 'Логин является обязательным полем', trigger: 'blur' },
        ],
        password: [{ required: true, message: 'Пароль является обязательным полем', trigger: 'blur' }],
        confirmPassword: [{ validator: validateConfirmPassword, trigger: 'blur' }],
      },
      permissionProps: {
        children: 'children',
        label: 'name',
        disabled: 'disabled',
      },
      permissions: [],
      menuPermissions: [],
      otherPermissions: [],
    };
  },
  computed: {
    normalizedMenuPermissions() {
      let tmp = [];
      this.currentUser.permissions.role.forEach(permission => {
        tmp.push({
          id: permission.id,
          name: permission.name,
          disabled: true, 
        });
      });
      const rolePermissions = {
        id: -1, // Just a faked ID
        name: 'Меню унаслендованные с роли',
        disabled: true,
        children: this.classifyPermissions(tmp).menu,
      };

      tmp = this.menuPermissions.filter(permission => !this.currentUser.permissions.role.find(p => p.id === permission.id));
      const userPermissions = {
        id: 0, // Faked ID
        name: 'Дополнительные доступы к меню',
        children: tmp,
        disabled: tmp.length === 0,
      };

      return [rolePermissions, userPermissions];
    },
    normalizedOtherPermissions() {
      let tmp = [];
      this.currentUser.permissions.role.forEach(permission => {
        tmp.push({
          id: permission.id,
          name: permission.name,
          disabled: true,
        });
      });
      const rolePermissions = {
        id: -1,
        name: 'Разрешения унаследованные с роли',
        disabled: true,
        children: this.classifyPermissions(tmp).other,
      };

      tmp = this.otherPermissions.filter(permission => !this.currentUser.permissions.role.find(p => p.id === permission.id));
      const userPermissions = {
        id: 0,
        name: 'Дополнительные разршешения',
        children: tmp,
        disabled: tmp.length === 0,
      };

      return [rolePermissions, userPermissions];
    },
    userMenuPermissions() {
      return this.classifyPermissions(this.userPermissions).menu;
    },
    userOtherPermissions() {
      return this.classifyPermissions(this.userPermissions).other;
    },
    userPermissions() {
      return this.currentUser.permissions.role.concat(this.currentUser.permissions.user);
    },

  },
  created() { 
    this.loadRoles();
    this.loadPlaces();
    this.resetNewUser();
    this.getList();
    if (checkPermission(['manage permission'])) {
      this.getPermissions();
    }
  },
  methods: {
    checkPermission,
    async getPermissions() {
      const { data } = await permissionResource.list({});
      const { all, menu, other } = this.classifyPermissions(data);
      this.permissions = all;
      this.menuPermissions = menu;
      this.otherPermissions = other;
    },

    async getList() {
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await userResource.list(this.query);
      this.list = data;
      this.list.forEach((element, index) => {
        element['index'] = (page - 1) * limit + index + 1;
      });
      this.total = meta.total;
      this.loading = false;
    },
    handleFilter() {
      this.query.page = 1;
      this.getList();
    },
    handleCreate() {
      this.resetNewUser();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['userForm'].clearValidate();
      });
    },
    handleDelete(id, name) {
      this.$confirm('This will permanently delete user ' + name + '. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        userResource.destroy(id).then(response => {
          this.$message({
            type: 'success',
            message: 'Delete completed',
          });
          this.handleFilter();
        }).catch(error => {
          console.log(error);
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Delete canceled',
        });
      });
    },
    async handleEditPermissions(id) {
      this.currentUserId = id;
      this.dialogPermissionLoading = true;
      this.dialogPermissionVisible = true;
      const found = this.list.find(user => user.id === id);
      const { data } = await userResource.permissions(id);
      this.currentUser = {
        id: found.id,
        name: found.name,
        permissions: data,
      };
      this.dialogPermissionLoading = false;
      this.$nextTick(() => {
        this.$refs.menuPermissions.setCheckedKeys(this.permissionKeys(this.userMenuPermissions));
        this.$refs.otherPermissions.setCheckedKeys(this.permissionKeys(this.userOtherPermissions));
      });
    },
    createUser() {
      this.$refs['userForm'].validate((valid) => {
        if (valid) {
          this.newUser.roles = [this.newUser.role];
          this.userCreating = true;
          userResource
            .store(this.newUser)
            .then(response => {
              this.$message({
                message: 'Новый пользователь ' + this.newUser.name + '(' + this.newUser.username + ') был успешно создан.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetEditingUser();
              this.dialogEditFormVisible = false;
              this.handleFilter();
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              this.userCreating = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    resetNewUser() {
      this.newUser = {
        name: '',
        username: '',
        password: '',
        confirmPassword: '',
        role: 'user',
      };
    },
    handleDownload() {
      this.downloading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['id', 'user_id', 'name', 'username', 'role'];
        const filterVal = ['index', 'id', 'name', 'username', 'role'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'user-list',
        });
        this.downloading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => v[j]));
    },
    permissionKeys(permissions) {
      return permissions.map(permssion => permssion.id);
    },
    classifyPermissions(permissions) {
      const all = []; const menu = []; const other = [];
      permissions.forEach(permission => {
        const permissionName = permission.name;
        all.push(permission);
        if (permissionName.startsWith('view menu')) {
          menu.push(this.normalizeMenuPermission(permission));
        } else {
          other.push(this.normalizePermission(permission));
        }
      });
      return { all, menu, other };
    },

    normalizeMenuPermission(permission) {
      return { id: permission.id, name: this.$options.filters.uppercaseFirst(permission.name.substring(10)), disabled: permission.disabled || false };
    },

    normalizePermission(permission) {
      const disabled = permission.disabled || permission.name === 'manage permission';
      return { id: permission.id, name: this.$options.filters.uppercaseFirst(permission.name), disabled: disabled };
    },

    confirmPermission() {
      const checkedMenu = this.$refs.menuPermissions.getCheckedKeys();
      const checkedOther = this.$refs.otherPermissions.getCheckedKeys();
      const checkedPermissions = checkedMenu.concat(checkedOther);
      this.dialogPermissionLoading = true;

      userResource.updatePermission(this.currentUserId, { permissions: checkedPermissions }).then(response => {
        this.$message({
          message: 'Permissions has been updated successfully',
          type: 'success',
          duration: 5 * 1000,
        });
        this.dialogPermissionLoading = false;
        this.dialogPermissionVisible = false;
      });
    }, 
    

    
    async loadRoles() {
      const res = await fetchRoles();
      this.nonAdminRoles = res.data;
    },
    async loadPlaces() {
      const {data} = await fetchPlaces(); 
      this.places = data;
    },

    async openEditUser(id_user) 
    {
      
      
      this.editUserId = id_user;
      const {data} = await userResource.get(id_user);

      const roleId = this.nonAdminRoles.find(r => r.name.indexOf(data.roles[0]) != -1).id;

      this.editUser = {
        id: data.id,
        username: data.username,
        job: data.job, 
        name: data.name,
        phone: data.phone,
        role:  roleId,
        places: data.places,
      }
      this.dialogEditFormVisible = true;
    }, 

    async updateUser()
    {
      this.editUser.roles = [this.editUser.role];
          userResource
            .update(this.editUser.id, this.editUser)
            .then(response => {
              this.$message({
                message: 'Пользователь ' + this.editUser.name + '(' + this.editUser.username + ') был успешно обновлён.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.dialogEditFormVisible = false;
              this.resetEditingUser();
              this.handleFilter();
            })
            .catch(error => {
              console.log(error);
            });
    }, 

    changePasswordUser(id_user) 
    {
      this.changePasswordId = id_user;
      this.changePassword.id = id_user;
      this.dialogPasswordFormVisible = true;
    }, 

    clearPasswordForm()
    {

      this.dialogPasswordFormVisible = false;
      this.changePasswordId = null;
      this.changePassword = {};
    },

    async updatePassword() { 
      this.changePassword.password = this.changePassword.password.trim();
      this.changePassword.confirmPassword = this.changePassword.confirmPassword.trim();
      if(this.changePassword.password == this.changePassword.confirmPassword && this.changePassword.password != '')
      {
       
        const res = await userResource.updatePassword(this.changePasswordId, this.changePassword);
        if(res.data)
        {
          this.$message({
            type:"success",
            message:"Пароль пользователя успешно обновлён!",
            duration: 5*1000
          });
          this.dialogPasswordFormVisible = false;
          this.changePasswordId  = null;
          this.changePassword = {};
        }
        else 
        {
          this.$message({
            type:'error',
            message:"Не получилось обновить пароль пользователя!",
            duration: 5*1000,
          });
        }
      }
      else 
      {
        this.$message({
          type:'error',
          message:"Не совпадает пароли!",
          duration: 5*1000,
        });
      }
    },

    resetEditingUser() {
      this.editUser = {};
      this.editUserId = null;
    },
  },
};
</script>

<style lang="scss" scoped>
.edit-input {
  padding-right: 100px;
}
.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
.dialog-footer {
  text-align: left;
  padding-top: 0;
  margin-left: 150px;
}
.app-container {
  flex: 1;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
  .block {
    float: left;
    min-width: 250px;
  }
  .clear-left {
    clear: left;
  }
}
</style>
