<template>
    <div class="app-container">
        
        <h4>
            Справочник Буровых станков
            <br>
            <el-input v-model="drillPagination.search" placeholder="Введите наименование" style="width: 300px;"></el-input>
            <el-input v-model="drillPagination.garage" placeholder="Введите Гаражный №" style="width: 250px;"></el-input>
            <el-input v-model="drillPagination.place"  placeholder="Введите Местоположение" style="width: 300px;"></el-input>
            <el-button type="success" @click="loadDrills">Поиск</el-button>
            <el-button v-if="checkPermission(['manage catalog-drill'])" style="float:right;" type="success" icon="el-icon-plus" @click="handleShowAddDrill">Добавить буровой станок</el-button>
        </h4>
        
        <el-table
            :data="drills"
            ref="drillsTable"
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
                    {{ drillGroups.length == 0 ? 
                            "" : 
                            drillGroups.find(g=> g.id == scope.row.id_group).name 
                    }}
                </template>
            </el-table-column>
            <el-table-column :min-width="12"    label="Тек. гар. номер"     prop="latest_drill_place.garage"        align="center"></el-table-column>
            <el-table-column :min-width="20"    label="Тек. местоположение" prop="latest_drill_place.place.title"   align="center"></el-table-column>
            <el-table-column :min-width="12"    label="Статус" align="center">
                <template slot-scope="scope">
                    {{ scope.row.status == 1 ? "Работает" : "Списан" }}
                </template>
            </el-table-column>
            <el-table-column :min-width="15"    label="Действия" align="center">
                <template slot-scope="scope">
                    <div  v-if="checkPermission(['manage catalog-drill'])" style="text-align: center;" >
                        <el-button type="info" icon="el-icon-edit" circle @click="handleShowEditDrill(scope.row.id)"></el-button>
                        <el-button type="danger" icon="el-icon-delete" circle @click="removeDrill(scope.row.id)"></el-button>
                    </div>
                    <div v-else> недосточно прав </div>
                </template>
            </el-table-column>
        </el-table>
        <Pagination v-show="drillPagination.total>0" :total="drillPagination.total" :page.sync="drillPagination.page" :limit.sync="drillPagination.per_page" @pagination="loadDrills" />
        <AddDrill v-if="checkPermission(['manage catalog-drill'])" :show="showAddDrill" :close="handleCloseAddDrill"></AddDrill>
        <EditDrill v-if="editingDrillId && checkPermission(['manage catalog-drill'])" :drillId="editingDrillId" :show="showEditDrill" :close="handleCloseEditDrill"></EditDrill>
    </div>
</template>


<script>
import {getAllDrills, getDrillGroups} from "@/api/drills";
import Pagination from '@/components/Pagination';
import { deleteDrill } from "@/api/drills";
import AddDrill from "./components/AddDrill.vue";
import EditDrill from "./components/EditDrill.vue";
import checkPermission from '@/utils/permission'; // Permission checking

export default {
    components: { Pagination, AddDrill, EditDrill },
    async created() {
        await this.loadTypes();
        await this.loadDrills();
    },
    data() {
        return {
            showAddDrill: false,

            showEditDrill: false, 
            editingDrillId: null,

            drillGroups: [],

            drillPagination: {
                place: ``,
                search: ``,
                garage: ``,
                page: 1,
                per_page: 10, 
                total:0,
                sort: `+id`,
            },

            drills: [
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
        async loadDrills() {
            const {pagination} = await getAllDrills(this.drillPagination);
            this.drillPagination = {
                place: this.drillPagination.place,
                search: this.drillPagination.search,
                garage: this.drillPagination.garage,
                page: pagination.current_page, 
                per_page: pagination.per_page,
                total: pagination.total, 
                sort: `+id`,
            };
            this.drills = pagination.data;
          
        },
        async loadTypes() {
            const {data} = await getDrillGroups();
            this.drillGroups = data;
        },
        handleShowAddDrill()
        {
            this.showAddDrill = true;
        }, 
        handleCloseAddDrill() {
            this.showAddDrill = false;
            this.loadDrills();
        },

        handleShowEditDrill(drillId) 
        {
            this.editingDrillId = drillId; 
            this.showEditDrill = true;
        }, 
        handleCloseEditDrill() 
        {
            this.editingDrillId = null;
            this.showEditDrill = false;
            this.loadDrills();
        }, 

        async removeDrill(drillId) {
            try { 
                const conf = await this.$confirm(`При удалении бурового станка все данные могут быть утеряны! Вы точно хотите удалить буровой станок?`,
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
            const res = await deleteDrill(drillId);
            if(res.status && !res.errors)
            {
                this.$message({
                    type:"success",
                    message:"Буровой станок удалён!", 
                    duration: 5*1000,
                });
                this.loadDrills();
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