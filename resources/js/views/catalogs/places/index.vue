<template>
    <div class="app-container">
        <h4>Справочник Площадок</h4>
        <br>
        <el-table
            row-key="id"
            :tree-props="{children: 'places', hasChildren: 'hasChildren'}"
            ref="divTable"
            :data="placesList"
            fit highlight-current-row border
        >
            <el-table-column                        :min-width="5"></el-table-column>
            <el-table-column label="ID"             :min-width="5" prop="id"></el-table-column>
            <el-table-column label="Наименование"   :min-width="75" prop="title"></el-table-column>
            <el-table-column label="Дейсивия"      :min-width="15">
                <template slot-scope="scope">
                        <div style="text-align: center;" v-if="checkPermission(['manage catalog-places'])">
                            <el-button type="success" icon="el-icon-plus" circle v-if="!scope.row.id_manager" @click="handleShowAddPlace(scope.row.id)"></el-button>
                            <el-button type="success" icon="el-icon-plus" circle v-else-if="!scope.row.id_parent" @click="handleShowAddChildPlace(scope.row)"></el-button>
                            <el-button type="info" icon="el-icon-edit" circle v-if="scope.row.id_manager" @click="handleShowEditPlace(scope.row)"></el-button>
                            <el-button type="danger" icon="el-icon-delete" circle v-if="scope.row.id_manager" @click="removePlaceHandler(scope.row.id)"></el-button>
                        </div>
                        <div v-else style="text-align: center;">не достаточно прав</div>
                </template>
            </el-table-column>
        </el-table>

        <el-dialog v-if="checkPermission(['manage catalog-places'])" title="Добавление площадки" :visible.sync="showAddPlace" width="60%;" :before-close="handleCloseAddPlace">
            <div class="form-container">
                <el-form ref="addPlaceForm" :model="addPlace" label-position="top">
                    <el-descriptions :column="1" border>
                        <el-descriptions-item >
                            <template slot="label">
                                Наименование
                            </template>
                            <el-input v-model="addPlace.title"></el-input>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                Порядок сортировки
                            </template>
                            <el-select v-model="addPlace.sort" style="width: 100%;">
                                <el-option :value="1" label="Первым элементом"></el-option>
                                <el-option 
                                    v-for="place in placesInManagerList"
                                    :key="place.id"
                                    :label="'После '+place.title"
                                    :value="place.sort+1"
                                >
                                </el-option>
                            </el-select>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                Экскаваторы
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="addPlace.excavator"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                Буровые установки
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="addPlace.drill"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                ЦПТ
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="addPlace.cpt"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                Автосамосвалы
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="addPlace.truck"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                ДСМ
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="addPlace.dsm"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                Локомотив
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="addPlace.locomotive"></el-switch>
                        </el-descriptions-item>
                    </el-descriptions>
                </el-form>
                <span slot="footer" class="dialog-footer" style="float:right;">
                    <el-button type="primary" @click="addPlaceHandler()">
                        Сохранить
                    </el-button>
                    <el-button type="danger" @click="handleCloseAddPlace()">
                        {{ $t('table.cancel') }}
                    </el-button>
                </span>
                <br><br>
            </div>
        </el-dialog>

        <el-dialog v-if="checkPermission(['manage catalog-places'])" title="Редактирование площадки" :visible.sync="showEditPlace" width="60%;" :before-close="handleCloseEditPlace">
            <div class="form-container">
                <el-form ref="editPlaceForm" :model="editPlace" label-position="top">
                    <el-descriptions :column="1" border>
                        <el-descriptions-item >
                            <template slot="label">
                                Наименование
                            </template>
                            <el-input v-model="editPlace.title"></el-input>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                Порядок сортировки
                            </template>
                            <el-select v-model="editPlace.sort" style="width: 100%;">
                                <el-option :value="1" label="Первым элементом"></el-option>
                                <el-option 
                                    v-for="place in placesInManagerList"
                                    :key="place.id"
                                    :label="'После '+place.title"
                                    :value="place.sort+1"
                                >
                                </el-option>
                            </el-select>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                Экскаваторы
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="editPlace.excavator"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                Буровые установки
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="editPlace.drill"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                ЦПТ
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="editPlace.cpt"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                Автосамосвалы
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="editPlace.truck"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                ДСМ
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="editPlace.dsm"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                Локомотив
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="editPlace.locomotive"></el-switch>
                        </el-descriptions-item>
                    </el-descriptions>
                </el-form>
                <span slot="footer" class="dialog-footer" style="float:right; ">
                    <el-button type="primary" @click="editPlaceHandler()">
                        Сохранить
                    </el-button>
                    <el-button type="danger" @click="handleCloseEditPlace()">
                        {{ $t('table.cancel') }}
                    </el-button>
                </span>
                <br><br>
            </div>
        </el-dialog>

      

    </div>
</template>

<script>
import {fetchPlaces, updatePlace, storePlace, deletePlace} from "@/api/places";
import checkPermission from '@/utils/permission'; // Permission checking

export default {
    created() {
        this.loadPlaces();
    },
    data() {
        return {
            showAddPlace: false,
            placesInManagerList: [],
            addPlace: {
                id_manager: null,
                title: "", 
                sort: 1,
                excavator: 0,
                drill: 0,
                cpt: 0,
                truck: 0,
                dsm: 0,
                locomotive: 0,
            },

          
            showEditPlace: false, 
            editPlace: {
                id_manager: null, 
                title: "",
                sort: 1,
            },

            placesList: [],
        }
    }, 
    methods: {
        checkPermission,
        async loadPlaces() {
            const {data} = await fetchPlaces();
            console.log(data);
            this.placesList = data;
        },


        handleShowAddPlace(managerId) {
            this.showAddPlace = true;
            this.placesInManagerList = this.placesList.find(m => m.id == managerId).places;

            this.addPlace = {
                id_manager: managerId,
                title: "", 
                sort: 1,
                excavator: 0,
                drill: 0,
                cpt: 0,
                truck: 0,
                dsm: 0,
                locomotive: 0,
            };

        }, 
        handleCloseAddPlace()
        {
            this.showAddPlace = false;  
            this.placesInManagerList = [];  
            this.addPlace = {
                id_manager: null,
                title: "", 
                sort: 1,
                excavator: 0,
                drill: 0,
                cpt: 0,
                truck: 0,
                dsm: 0,
                locomotive: 0,
            };        
        }, 
        async addPlaceHandler()
        {
            const res = await storePlace(this.addPlace);

            if(!res.errors && res.status) 
            {
                this.$message({
                    message:"Площадка успешно добавлена!", 
                    type:'success',
                    duration: 5*1000,
                });
                this.handleCloseAddPlace();
                this.loadPlaces();
                return;
            }
            
            this.$message({
                message:res.message, 
                type:'error',
                duration: 5*1000,
            });
             
        },

        handleShowAddChildPlace(place) 
        {
            this.showAddPlace = true; 
            this.placesInManagerList = this.placesList.flatMap(p=>p.places).find(p => p == place).places;

            this.addPlace = {
                id_manager: place.id_manager,
                title: "",
                id_parent: place.id, 
                sort: 1,
                excavator: place.excavator,
                drill: place.drill,
                cpt: place.cpt,
                truck: place.truck,
                dsm: place.dsm,
                locomotive: place.locomotive,
            };
        },


        handleShowEditPlace(place) 
        {
            console.log(place);
            if(place.id_parent) 
            {
                this.placesInManagerList = this.placesList
                                            .find(m => m.id == place.id_manager)
                                            .places
                                            .find(p => p.id == place.id_parent)
                                            .places
                                            .filter(p => p.sort != place.sort);
            }
            else 
            {
                this.placesInManagerList = this.placesList
                                            .find(m => m.id == place.id_manager)
                                            .places
                                            .filter(p => p.sort != place.sort);
            }
            this.editPlace = place;
            this.showEditPlace = true;
        }, 
        handleCloseEditPlace() 
        {
            this.placesInManagerList = []; 
            this.editPlace = { 
                id_manager: null, 
                title: "",
                sort: 1,
                excavator: 0,
                drill: 0,
                cpt: 0,
                truck: 0,
                dsm: 0,
                locomotive: 0,
            };
            this.showEditPlace = false;
        },

        async editPlaceHandler() {
            // TODO: добавить после добавления API
            const res = await updatePlace(this.editPlace.id, this.editPlace);

            if(!res.errors && res.status) 
            {
                this.$message({
                    message:"Площадка успешно обновлена!", 
                    type:'success',
                    duration: 5*1000,
                });
                this.handleCloseEditPlace();
                this.loadPlaces();
                return;
            }
            this.$message({
                message:res.message, 
                type:'error',
                duration: 5*1000,
            });
        },
      
        async removePlaceHandler(placeId) 
        {
            try { 
                let conf = await this.$confirm(`Вы точно хотите удалить эту площадку? 
                                                Все связанные данные с этой площадкой 
                                                могут быть утеряны.`,
                                                'Внимание!', {type:"warning"});
                if(conf != "confirm")
                    return;
            }
            catch(e) 
            {
                console.log(e);
                return;
            }

            const res = await deletePlace(placeId);
            if(!res.errors && res.status) 
            {
                this.$message({
                    message:"Площадка успешно удалена!", 
                    type:'success',
                    duration: 5*1000,
                });
                this.loadPlaces();
                return;
            }
            this.$message({
                message:res.message, 
                type:'error',
                duration: 5*1000,
            });
        }
    },


};

</script>
