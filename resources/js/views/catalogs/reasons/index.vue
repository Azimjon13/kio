<template>
    <div class="app-container">
        <h3 style="text-align: center;">Виды простоев</h3>
        <div class="block">
            <div>
                <el-input v-model="reasonsFilter.search" style="width: 15%;" placeholder="Введите для поиска"></el-input>
                <el-select v-model="reasonsFilter.type">
                    <el-option :value="null" label="Все"></el-option>
                    <el-option :value="1" label="Плановые"></el-option>
                    <el-option :value="2" label="Внеплановые"></el-option>
                    <el-option :value="3" label="Эксп. вне объёма"></el-option>
                </el-select>
                <el-select v-model="reasonsFilter.technica_type">
                    <el-option :value="null" label="Все"></el-option>
                    <el-option v-for="t in technica_types" :value="t.aggregate" :label="t.title" ></el-option>
                </el-select>
                

                <el-button type="success" @click="loadReasons">Поиск</el-button>
                <el-button v-if="checkPermission(['manage catalog-reasons'])" type="success" icon="el-icon-plus" style="float:right;" @click="handleOpenAddReason">Добавить вид простоя</el-button>
            </div>
            
            <br>
            <el-table
                :data="reasonsList"
                ref="reasonsTable"
                fit  highlight-current-row border style="width: 100%;" align="center"
            >
                <el-table-column :min-width="4"   label="ID"              prop = "id" align="center"></el-table-column>
                <el-table-column :min-width="8"  label="Тип"             prop = "type" align="center">
                    <template slot-scope="scope">
                        {{ scope.row.type == 1 ? "Плановый" : (scope.row.type == 2 ? "Внеплановый" : "Эксп. вне объёма") }}
                    </template>
                </el-table-column>
                <el-table-column :min-width="20"  label="Наименование"    prop = "reason" align="center"></el-table-column>
                <el-table-column :min-width="54"  label="Применяется для следующих техник" align="center"> 
                    <el-table-column :min-width="9"  label="Эксков-ы"      prop = "excavator" align="center">
                        <template slot-scope="scope">
                            {{ scope.row.excavator == 1 ? "Да" : "Нет" }}
                        </template>
                    </el-table-column>
                    <el-table-column :min-width="9"  label="Буров. станки"           prop = "drill" align="center">
                        <template slot-scope="scope">
                            {{ scope.row.drill == 1 ? "Да" : "Нет" }}
                        </template>
                    </el-table-column>
                    <el-table-column :min-width="9"  label="ЦПТ"           prop = "cpt" align="center">
                        <template slot-scope="scope">
                            {{ scope.row.cpt == 1 ? "Да" : "Нет" }}
                        </template>
                    </el-table-column>
                    <el-table-column :min-width="9"  label="Самосвалы"     prop = "truck" align="center">
                        <template slot-scope="scope">
                            {{ scope.row.truck == 1 ? "Да" : "Нет" }}
                        </template>
                    </el-table-column>
                    <el-table-column :min-width="9"  label="ДСМ"           prop = "dsm" align="center">
                        <template slot-scope="scope">
                            {{ scope.row.dsm == 1 ? "Да" : "Нет" }}
                        </template>
                    </el-table-column>
                    <el-table-column :min-width="9"  label="Тепловозы"     prop = "locomotive" align="center">
                        <template slot-scope="scope">
                            {{ scope.row.locomotive == 1 ? "Да" : "Нет" }}
                        </template>
                    </el-table-column>
                </el-table-column>
                <el-table-column :min-width="14"  label="Доп. ф-ии" align="center">
                    <template slot-scope="scope">
                        <div style="text-align: center;" v-if="checkPermission(['manage catalog-reasons'])">
                            <el-button type="info"      icon="el-icon-edit" circle @click="handleShowEditReason(scope.row.id)"></el-button>
                            <el-button type="danger"    icon="el-icon-delete" circle @click="handleDeleteReason(scope.row.id)"></el-button>
                        </div>
                        <div v-else style="text-align: center;">недосточно прав</div>
                    </template>
                </el-table-column>
            </el-table>
            <Pagination :page-sizes="[5, 7, 10, 15, 20]" v-show="reasonsFilter.total>0" :total="reasonsFilter.total" :page.sync="reasonsFilter.page" :limit.sync="reasonsFilter.per_page" @pagination="loadReasons" />
            <AddReason  v-if="checkPermission(['manage catalog-reasons'])" :show="showAddReason" :close="handleCloseAddReason"></AddReason>
            <EditReason v-if="editReasonId && checkPermission(['manage catalog-reasons'])" :reasonId="editReasonId" :show="showEditReason" :close="handleCloseEditReason" ></EditReason>
            <hr>
            <br/>
        </div>
        <h3 style="text-align: center;">Коды простоев</h3>
        <div class="block">
            <div>
                <el-input v-model="codesFilter.search" style="width: 15%;" placeholder="Введите для поиска"></el-input>
                <el-select v-model="codesFilter.technica_type" @change="handleSelectTechnicaType">
                    <el-option :value="null" label="Все"></el-option>
                    <el-option v-for="tp in technica_types" :value="tp.id" :label="tp.title"></el-option>
                </el-select>
                <el-select v-model="codesFilter.technica_group" v-if="codesFilter.technica_type">
                    <el-option :value="null" label="Все"></el-option>
                    <el-option v-for="gp in groups" :value="gp.id" :label="gp.name"></el-option>
                </el-select>
                <el-select v-model="codesFilter.id_reason" v-if="codesFilter.technica_type">
                    <el-option :value="null" label="Все"></el-option>
                    <el-option v-for="reason in reasonsListForCodes" :value="reason.id" :label="reason.reason"></el-option>
                </el-select>
                
                <el-button type="success" @click="loadCodes">Поиск</el-button>
                <el-button type="success" style="float: right;" @click="handleShowAddCode" v-if="checkPermission(['manage catalog-reasons'])">Добавить код простоя</el-button>
            </div>
            <br>
            <el-table 
                :data="codesList"
                ref="codesTable"
                fit  highlight-current-row border style="width: 100%;" align="center"
            >   
                <el-table-column :min-width="5"  label="ID"     prop="id"   align="center">   
                </el-table-column>
                <el-table-column :min-width="8" label="Код"    prop="code" align="center">
                </el-table-column>
                <el-table-column :min-width="35" label="Описание"     prop="title" align="center">
                </el-table-column>
                <el-table-column :min-width="20" label="Вид причины простоя"  prop="reason" align="center">
                </el-table-column>
                <el-table-column :min-width="20" label="Тип и вид техники"     prop="technica_group_name" align="center">
                </el-table-column> 
                <el-table-column :min-width="12" label="Действия"  align="center">
                    <template slot-scope="scope">
                        <div style="text-align: center;" v-if="checkPermission(['manage catalog-reasons'])">
                            <el-button type="info" icon="el-icon-edit" circle @click="handleShowEditCode(scope.row.id)" ></el-button>
                            <el-button type="danger" icon="el-icon-delete" circle @click="handleDeleteCode(scope.row.id)" ></el-button>
                        </div>
                        <div style="text-align: center;" v-else> недосточно прав </div>
                    </template>
                </el-table-column>       
            </el-table>
            <AddCode v-if="checkPermission(['manage catalog-reasons'])" :show="showAddCode" :close="handleCloseAddCode"></AddCode>
            <EditCode v-if="editCodeId && checkPermission(['manage catalog-reasons'])" :codeId="editCodeId" :show="showEditCode" :close="handleCloseEditCode"></EditCode>
            <Pagination :page-sizes="[5, 7, 10, 15, 20]" v-show="codesFilter.total>0" :total="codesFilter.total" :page.sync="codesFilter.page" :limit.sync="codesFilter.per_page" @pagination="loadCodes" :autoScroll="false"></Pagination>
        </div>
    </div>
</template>


<script>
import { getAllReasons, deleteReason, reasonsByTechnicaTypeId } from '@/api/reasons';
import Pagination from '@/components/Pagination';
import { technicaGroups, technicaTypes } from '@/api/technica';
import { getAllCodes, deleteCode } from "@/api/codes";
import AddReason from './components/AddReason.vue';
import EditReason from './components/EditReason.vue';
import AddCode from './components/AddCode.vue';
import EditCode from './components/EditCode.vue';
import checkPermission from '@/utils/permission'; // Permission checking
export default { 

    created() {
        this.loadReasons();
        this.loadTypes();
        this.loadGroups();
        this.loadCodes();
    },
    data() {
        return {
            showAddReason: false,


            showEditCode: false, 
            editCodeId: null, 

            showEditReason: false,
            editReasonId: null, 

            showAddCode: false,


            reasonsFilter: {
                search: "",
                type: null, 
                technica_type: null,
                page: 1, 
                total: 0, 
                per_page: 5,
            },

            codesFilter: {
                search: "",
                technica_type: null,
                technica_group: null,
                id_reason: null,
                page: 1, 
                total: 0, 
                per_page: 5,
            },
            reasonsListForCodes: [],


            technica_types: [],
            technica_groups: [],
            groups: [],
            reasonsList: [],
            codesList: [],
        }
      
    },
    methods: {        
        checkPermission,
        async loadReasons() 
        {
                const {pagination} = await getAllReasons(this.reasonsFilter);
                this.reasonsFilter = {
                    ...this.reasonsFilter,
                    page: pagination.current_page,
                    total: pagination.total,
                    per_page: pagination.per_page,
                }
                this.reasonsList = pagination.data;
        },
        handleCloseAddReason()
        {
            this.showAddReason = false;
            this.loadReasons();
        }, 
        handleOpenAddReason()
        {
            this.showAddReason = true;
        }, 
        handleShowEditReason(reasoinId)
        {
                this.editReasonId = reasoinId;
                this.showEditReason = true;
        }, 
        handleCloseEditReason()
        {
            this.showEditReason = false;
            this.editReasonId = null;
            this.loadReasons();
        }, 
        async handleDeleteReason(reasoinId) 
        {
            try  { 
                const conf = await this.$confirm(`При удалении причины простоя, могут быть утеряны все данные связанные с этой причиной. Вы точно хотите удалить причину простоя?`, 'Внимание', {type:"error"});
                if(conf != "confirm")
                    return;
            }
            catch(e)
            {
                console.log(e);
                return;
            }

            const res = await deleteReason(reasoinId);
            if(!res.errors && res.status) 
            {
                this.$message({
                    type:"success", 
                    message:"Удаление причины простоя прошла успешно!",
                    duration: 5*1000,
                });
                this.loadReasons();
                return;
            }
            this.$message({
                type:"error", 
                message:res.message,
                duration: 5*1000,
            });
        }, 

        // Codes

     

        async loadCodes() {
            const {pagination} = await getAllCodes(this.codesFilter);
            this.codesFilter = {
                ...this.codesFilter,
                page: pagination.current_page, 
                total: pagination.total, 
                per_page: pagination.per_page,
            };  
            this.codesList = pagination.data;
            console.log(pagination);


        },

        async loadTypes() {
            const {data} = await technicaTypes();
            this.technica_types = data;
            console.log(data);
        },
        async loadGroups() {
            const {data} = await technicaGroups();
            this.technica_groups = data;
            console.log(data);
        },

        async handleSelectTechnicaType(technicaTypeId)
        {
            if(technicaTypeId != null)
            {
                this.groups = this.technica_groups.filter(g => g.id_technica_type == technicaTypeId);
                const {data} = await reasonsByTechnicaTypeId(technicaTypeId);
                this.reasonsListForCodes = data;
            }
            else 
            {
                this.reasonsListForCodes = [];
                this.groups = [];
            }
            this.codesFilter.technica_group = null;
            this.codesFilter.id_reason = null;
            
        }, 

        handleCloseAddCode()
        {
            this.showAddCode = false;
            this.loadCodes();
        },
        handleShowAddCode() 
        {
            this.showAddCode = true;
            this.loadCodes();
        },

        handleShowEditCode(codeId) 
        {
            this.editCodeId = codeId;
            this.showEditCode = true;
        },
        handleCloseEditCode()
        {
            this.editCodeId = null;
            this.showEditCode = false;
            this.loadCodes();
        }, 

        async handleDeleteCode(codeId) 
        {
            try {
                const conf = await this.$confirm(`Удаление кода-простоя может привести к утере данных связанных с этим простоем. Вы точно уверены что хотите удалить код простоя?`, 'Внимание!', {type:'error'});
                if(conf != "confirm")
                    return;
            }   
            catch(e)
            {
                console.log(e);
                return;
            }
            const res = await deleteCode(codeId);
            if(!res.errors && res.status) 
            {
                this.$message({
                    message:"Удаление кода прошла успешно!",
                    type:"success",
                    duration: 5*1000,
                });
                this.loadCodes();
                return;
            }
            this.$message({
                message:res.message,
                type:"error",
                duration: 5*1000,
            });
        },

    }, 
    components: { Pagination, AddReason, EditReason, AddCode, EditCode },

}
</script>