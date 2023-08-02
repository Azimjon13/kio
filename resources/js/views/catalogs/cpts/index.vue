<template>
    <div class="app-container">
        
        <h4>
            Справочник ЦПТ
            <br>
            <el-input v-model="cptPagination.search" placeholder="Введите наименование" style="width: 300px;"></el-input>
            <el-input v-model="cptPagination.garage" placeholder="Введите Гаражный №" style="width: 250px;"></el-input>
            <el-input v-model="cptPagination.place"  placeholder="Введите Местоположение" style="width: 300px;"></el-input>
            <el-button type="success" @click="loadCpts">Поиск</el-button>
            <el-button v-if="checkPermission(['manage catalog-cpt'])" style="float:right;" type="success" icon="el-icon-plus" @click="handleShowAddCpt">Добавить ЦПТ</el-button>
        </h4>
        
        <el-table
            :data="cpts"
            ref="cptsTable"
            fit
            highlight-current-row
            border
            style="width: 100%;"
            align="center"
        >
            <el-table-column :min-width="8"     label="ID"                  prop="id"       align="center"></el-table-column>
            <el-table-column :min-width="25"    label="Наименование"        prop="title"    align="center"></el-table-column>
            <el-table-column :min-width="8"     label="Тип"                                 align="center">
                <template slot-scope="scope">
                    {{ cptGroups.length == 0 ? 
                            "" : 
                            cptGroups.find(g=> g.id == scope.row.id_group).name 
                    }}
                </template>
            </el-table-column>
            <el-table-column :min-width="12"    label="Тек. гар. номер"     prop="latest_cpt_place.garage"        align="center"></el-table-column>
            <el-table-column :min-width="20"    label="Тек. местоположение" prop="latest_cpt_place.place.title"   align="center"></el-table-column>
            <el-table-column :min-width="12"    label="Статус" align="center">
                <template slot-scope="scope">
                    {{ scope.row.status == 1 ? "Работает" : "Списан" }}
                </template>
            </el-table-column>
            <el-table-column :min-width="15"    label="Действия" align="center">
                <template slot-scope="scope">
                    <div  v-if="checkPermission(['manage catalog-cpt'])" style="text-align: center;" >
                        <el-button type="info" icon="el-icon-edit" circle @click="handleShowEditCpt(scope.row.id)"></el-button>
                        <el-button type="danger" icon="el-icon-delete" circle @click="removeCpt(scope.row.id)"></el-button>
                    </div>
                    <div v-else> недосточно прав </div>
                </template>
            </el-table-column>
        </el-table>
        <Pagination v-show="cptPagination.total>0" :total="cptPagination.total" :page.sync="cptPagination.page" :limit.sync="cptPagination.per_page" @pagination="loadCpts" />
        <AddCpt v-if="checkPermission(['manage catalog-cpt'])" :show="showAddCpt" :close="handleCloseAddCpt"></AddCpt>
        <EditCpt v-if="editingCptId && checkPermission(['manage catalog-cpt'])" :cptId="editingCptId" :show="showEditCpt" :close="handleCloseEditCpt"></EditCpt>
    </div>
</template>


<script>
import {getAllCpts, getCptGroups} from "@/api/cpts";
import Pagination from '@/components/Pagination';
import { deleteCpt } from "@/api/cpts";
import AddCpt from "./components/AddCpt.vue";
import EditCpt from "./components/EditCpt.vue";
import checkPermission from '@/utils/permission'; // Permission checking

export default {
    components: { Pagination, AddCpt, EditCpt },
    async created() {
        await this.loadTypes();
        await this.loadCpts();
    },
    data() {
        return {
            showAddCpt: false,

            showEditCpt: false, 
            editingCptId: null,

            cptGroups: [],

            cptPagination: {
                place: ``,
                search: ``,
                garage: ``,
                page: 1,
                per_page: 10, 
                total:0,
                sort: `+id`,
            },

            cpts: [
                {
                    id: 1, 
                    id_group: 1, 
                    title: "ЭКГ - 1", 
                    status: 1, 
                }
            ], 
            
        };
    },
    methods: {
        checkPermission,
        async loadCpts() {
            const {pagination} = await getAllCpts(this.cptPagination);
            this.cptPagination = {
                place: this.cptPagination.place,
                search: this.cptPagination.search,
                garage: this.cptPagination.garage,
                page: pagination.current_page, 
                per_page: pagination.per_page,
                total: pagination.total, 
                sort: `+id`,
            };
            this.cpts = pagination.data;
          
        },
        async loadTypes() {
            const {data} = await getCptGroups();
            this.cptGroups = data;
        },
        handleShowAddCpt()
        {
            this.showAddCpt = true;
        }, 
        handleCloseAddCpt() {
            this.showAddCpt = false;
            this.loadCpts();
        },

        handleShowEditCpt(cptId) 
        {
            this.editingCptId = cptId; 
            this.showEditCpt = true;
        }, 
        handleCloseEditCpt() 
        {
            this.editingCptId = null;
            this.showEditCpt = false;
            this.loadCpts();
        }, 

        async removeCpt(cptId) {
            try { 
                const conf = await this.$confirm(`При удалении ЦПТ все данные могут быть утеряны! Вы точно хотите удалить ЦПТ?`,
                                                    `Внимание`, 
                                                    {
                                                        type:"danger",
                                                    });
                if(conf != "confirm")
                    return;

            }
            catch(e)
            {
                console.log(e);
                return;
            }
            const res = await deleteCpt(cptId);
            if(res.status && !res.errors)
            {
                this.$message({
                    type:"success",
                    message:"ЦПТ удалён!", 
                    duration: 5*1000,
                });
                this.loadCpts();
                return;
            }
            this.$message({
                type:"error",
                message:res.message, 
                duration: 5*1000,
            });
            return;
        }
    }
}

</script>