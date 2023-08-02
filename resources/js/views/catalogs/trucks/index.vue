<template>
    <div class="app-container">
        
        <h4>
            Справочник Автосамосвалов
            <br>
            <el-input v-model="truckPagination.search" placeholder="Введите наименование" style="width: 300px;"></el-input>
            <el-input v-model="truckPagination.garage" placeholder="Введите Гаражный №" style="width: 250px;"></el-input>
            <el-input v-model="truckPagination.place"  placeholder="Введите Местоположение" style="width: 300px;"></el-input>
            <el-button type="success" @click="loadTrucks">Поиск</el-button>
            <el-button v-if="checkPermission(['manage catalog-truck'])" style="float:right;" type="success" icon="el-icon-plus" @click="handleShowAddTruck">Добавить автосамосвал</el-button>
        </h4>
        
        <el-table
            :data="trucks"
            ref="trucksTable"
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
                    {{ truckGroups.length == 0 ? 
                            "" : 
                            truckGroups.find(g=> g.id == scope.row.id_group).name 
                    }}
                </template>
            </el-table-column>
            <el-table-column :min-width="12"    label="Тек. гар. номер"     prop="latest_truck_place.garage"        align="center"></el-table-column>
            <el-table-column :min-width="20"    label="Тек. местоположение" prop="latest_truck_place.place.title"   align="center"></el-table-column>
            <el-table-column :min-width="12"    label="Статус" align="center">
                <template slot-scope="scope">
                    {{ scope.row.status == 1 ? "Работает" : "Списан" }}
                </template>
            </el-table-column>
            <el-table-column :min-width="15"    label="Действия" align="center">
                <template slot-scope="scope">
                    <div  v-if="checkPermission(['manage catalog-truck'])" style="text-align: center;" >
                        <el-button type="info" icon="el-icon-edit" circle @click="handleShowEditTruck(scope.row.id)"></el-button>
                        <el-button type="danger" icon="el-icon-delete" circle @click="removeTruck(scope.row.id)"></el-button>
                    </div>
                    <div v-else> недосточно прав </div>
                </template>
            </el-table-column>
        </el-table>
        <Pagination v-show="truckPagination.total>0" :total="truckPagination.total" :page.sync="truckPagination.page" :limit.sync="truckPagination.per_page" @pagination="loadTrucks" />
        <AddTruck v-if="checkPermission(['manage catalog-truck'])" :show="showAddTruck" :close="handleCloseAddTruck"></AddTruck>
        <EditTruck v-if="editingTruckId && checkPermission(['manage catalog-truck'])" :truckId="editingTruckId" :show="showEditTruck" :close="handleCloseEditTruck"></EditTruck>
    </div>
</template>


<script>
import {getAllTrucks, getTruckGroups} from "@/api/trucks";
import Pagination from '@/components/Pagination';
import { deleteTruck } from "@/api/trucks";
import AddTruck from "./components/AddTruck.vue";
import EditTruck from "./components/EditTruck.vue";
import checkPermission from '@/utils/permission'; // Permission checking

export default {
    components: { Pagination, AddTruck, EditTruck },
    async created() {
        await this.loadTypes();
        await this.loadTrucks();
    },
    data() {
        return {
            showAddTruck: false,

            showEditTruck: false, 
            editingTruckId: null,

            truckGroups: [],

            truckPagination: {
                place: ``,
                search: ``,
                garage: ``,
                page: 1,
                per_page: 10, 
                total:0,
                sort: `+id`,
            },

            trucks: [
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
        async loadTrucks() {
            const {pagination} = await getAllTrucks(this.truckPagination);
            this.truckPagination = {
                place: this.truckPagination.place,
                search: this.truckPagination.search,
                garage: this.truckPagination.garage,
                page: pagination.current_page, 
                per_page: pagination.per_page,
                total: pagination.total, 
                sort: `+id`,
            };
            this.trucks = pagination.data;
          
        },
        async loadTypes() {
            const {data} = await getTruckGroups();
            this.truckGroups = data;
        },
        handleShowAddTruck()
        {
            this.showAddTruck = true;
        }, 
        handleCloseAddTruck() {
            this.showAddTruck = false;
            this.loadTrucks();
        },

        handleShowEditTruck(truckId) 
        {
            this.editingTruckId = truckId; 
            this.showEditTruck = true;
        }, 
        handleCloseEditTruck() 
        {
            this.editingTruckId = null;
            this.showEditTruck = false;
            this.loadTrucks();
        }, 

        async removeTruck(truckId) {
            try { 
                const conf = await this.$confirm(`При удалении автосамосвала все данные могут быть утеряны! Вы точно хотите удалить автосамосвал?`,
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
            const res = await deleteTruck(truckId);
            if(res.status && !res.errors)
            {
                this.$message({
                    type:"success",
                    message:"Автосамосвал удалён!", 
                    duration: 5*1000,
                });
                this.loadTrucks();
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