<template>
    <div class="app-container">
        
        <h4>
            Справочник ДСМ
            <br>
            <el-input v-model="dsmPagination.search" placeholder="Введите наименование" style="width: 300px;"></el-input>
            <el-input v-model="dsmPagination.garage" placeholder="Введите Гаражный №" style="width: 250px;"></el-input>
            <el-input v-model="dsmPagination.place"  placeholder="Введите Местоположение" style="width: 300px;"></el-input>
            <el-button type="success" @click="loadDsms">Поиск</el-button>
            <el-button v-if="checkPermission(['manage catalog-dsm'])" style="float:right;" type="success" icon="el-icon-plus" @click="handleShowAddDsm">Добавить ДСМ</el-button>
        </h4>
        
        <el-table
            :data="dsms"
            ref="dsmsTable"
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
                    {{ dsmGroups.length == 0 ? 
                            "" : 
                            dsmGroups.find(g=> g.id == scope.row.id_group).name 
                    }}
                </template>
            </el-table-column>
            <el-table-column :min-width="12"    label="Тек. гар. номер"     prop="latest_dsm_place.garage"        align="center"></el-table-column>
            <el-table-column :min-width="20"    label="Тек. местоположение" prop="latest_dsm_place.place.title"   align="center"></el-table-column>
            <el-table-column :min-width="12"    label="Статус" align="center">
                <template slot-scope="scope">
                    {{ scope.row.status == 1 ? "Работает" : "Списан" }}
                </template>
            </el-table-column>
            <el-table-column :min-width="15"    label="Действия" align="center">
                <template slot-scope="scope">
                    <div  v-if="checkPermission(['manage catalog-dsm'])" style="text-align: center;" >
                        <el-button type="info" icon="el-icon-edit" circle @click="handleShowEditDsm(scope.row.id)"></el-button>
                        <el-button type="danger" icon="el-icon-delete" circle @click="removeDsm(scope.row.id)"></el-button>
                    </div>
                    <div v-else> недосточно прав </div>
                </template>
            </el-table-column>
        </el-table>
        <Pagination v-show="dsmPagination.total>0" :total="dsmPagination.total" :page.sync="dsmPagination.page" :limit.sync="dsmPagination.per_page" @pagination="loadDsms" />
        <AddDsm v-if="checkPermission(['manage catalog-dsm'])" :show="showAddDsm" :close="handleCloseAddDsm"></AddDsm>
        <EditDsm v-if="editingDsmId && checkPermission(['manage catalog-dsm'])" :dsmId="editingDsmId" :show="showEditDsm" :close="handleCloseEditDsm"></EditDsm>
    </div>
</template>


<script>
import {getAllDsms, getDsmGroups} from "@/api/dsms";
import Pagination from '@/components/Pagination';
import { deleteDsm } from "@/api/dsms";
import AddDsm from "./components/AddDsm.vue";
import EditDsm from "./components/EditDsm.vue";
import checkPermission from '@/utils/permission'; // Permission checking

export default {
    components: { Pagination, AddDsm, EditDsm },
    async created() {
        await this.loadTypes();
        await this.loadDsms();
    },
    data() {
        return {
            showAddDsm: false,

            showEditDsm: false, 
            editingDsmId: null,

            dsmGroups: [],

            dsmPagination: {
                place: ``,
                search: ``,
                garage: ``,
                page: 1,
                per_page: 10, 
                total:0,
                sort: `+id`,
            },

            dsms: [
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
        async loadDsms() {
            const {pagination} = await getAllDsms(this.dsmPagination);
            this.dsmPagination = {
                place: this.dsmPagination.place,
                search: this.dsmPagination.search,
                garage: this.dsmPagination.garage,
                page: pagination.current_page, 
                per_page: pagination.per_page,
                total: pagination.total, 
                sort: `+id`,
            };
            this.dsms = pagination.data;
          
        },
        async loadTypes() {
            const {data} = await getDsmGroups();
            this.dsmGroups = data;
        },
        handleShowAddDsm()
        {
            this.showAddDsm = true;
        }, 
        handleCloseAddDsm() {
            this.showAddDsm = false;
            this.loadDsms();
        },

        handleShowEditDsm(dsmId) 
        {
            this.editingDsmId = dsmId; 
            this.showEditDsm = true;
        }, 
        handleCloseEditDsm() 
        {
            this.editingDsmId = null;
            this.showEditDsm = false;
            this.loadDsms();
        }, 

        async removeDsm(dsmId) {
            try { 
                const conf = await this.$confirm(`При удалении ДСМ все данные могут быть утеряны! Вы точно хотите удалить ДСМ?`,
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
            const res = await deleteDsm(dsmId);
            if(res.status && !res.errors)
            {
                this.$message({
                    type:"success",
                    message:"ДСМ удалён!", 
                    duration: 5*1000,
                });
                this.loadDsms();
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