<template>
    <el-dialog title="Добавление Автосамосвала" width="40%" :visible.sync="this.show" :before-close="close">
        <div class="form-container">
            <el-form ref="addTruckForm" :model="truck" label-position="top" >
                <el-descriptions :column="1" border>
                    <el-descriptions-item >
                        <template slot="label">
                           Наименование
                        </template>
                        <el-input v-model="truck.title"></el-input>
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                           Тип автосамосвала
                        </template>
                        <el-select v-model="truck.id_group">
                            <el-option 
                                v-for="group in truckGroups" 
                                :value="group.id" 
                                :label="group.name"
                            ></el-option>
                        </el-select>
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                           Подразделение
                        </template>
                        <el-select v-model="truck.id_division" @change="handleSelectManager">
                            <el-option 
                                v-for="manager in managers" 
                                :value="manager.id" 
                                :label="manager.title"
                            ></el-option>
                        </el-select>
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                           Рудник
                        </template>
                        <el-select v-model="truck.id_rudnik" @change="handleSelectRudnik">
                            <el-option 
                                v-for="rundik in rudniks" 
                                :value="rundik.id" 
                                :label="rundik.title"
                            ></el-option>
                        </el-select>
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                            Площадка
                        </template>
                        <el-select v-model="truck.id_place">
                            <el-option 
                                v-for="place in places" 
                                :value="place.id"
                                :label="place.title"
                            ></el-option>
                        </el-select>
                    </el-descriptions-item>
                    <el-descriptions-item >
                        <template slot="label">
                            Гараж №
                        </template>
                        <el-input v-model="truck.garage"></el-input>
                    </el-descriptions-item>
                    <el-descriptions-item >
                        <template slot="label">
                            Приступил к работе с:
                        </template>
                        <el-date-picker
                                value-format="yyyy-MM-dd"
                                v-model="truck.date_from"
                                type="date"
                                placeholder="Выберите дату"
                                style="width: 100%;"
                            >
                        </el-date-picker>
                    </el-descriptions-item>
                </el-descriptions>
            </el-form>
            <br>
            <span slot="footer" class="dialog-footer" style="float:right;">
                <el-button type="primary" @click="saveTruck">
                    Добавить
                </el-button>
                <el-button type="danger" @click="close">
                    {{ $t('table.cancel') }}
                </el-button>
            </span>
            <br><br>
        </div>
    </el-dialog>
</template>


<script>
import {storeTruck, getTruckGroups} from "@/api/trucks";
import {fetchPlaces} from "@/api/places";

export default { 
    props: {
        show: Boolean,
        close: Function
    },
    async created() {
        await this.loadTypes();
        this.loadManagers();
    },
    data() {
        return {
            truckGroups: [],
            placesList: [],
            managers: [],
            rudniks: [],
            places: [],
            truck: {
                title: "",
                id_division: null, 
                id_rudnik: null,
                id_place: null,
                garage: "",
                date_from: "",
                status: 1,
            },
        }
    },
    methods: {
        async loadTypes() {
            const {data} = await getTruckGroups();
            this.truckGroups = data;
        },
    
        async loadManagers() {
            const {data} = await fetchPlaces();
            this.placesList = data.flatMap(m=>m.places);
            this.managers = data.map(m => ({id:m.id, title:m.title}));
            console.log(this.placesList);
        },

        handleSelectManager(managerId) {
            this.rudniks = this.placesList.filter(p=>p.id_manager == managerId); ;
            this.truck.id_place = null;
        },

        handleSelectRudnik(rundikId)
        {
            this.places = this.rudniks.find(r => r.id == rundikId).places;
            this.truck.id_place = null;
        }, 

        async saveTruck() {
            const res = await storeTruck(this.truck);
            console.log(res);
            if(res.status && res.errors == null)
            {
                this.$message({
                    message: 'Автосамосвал успешно добавлен!',
                    type: 'success',
                    duration: 5 * 1000,
                });
                this.close();
                return;
            } 
            this.$message({
                message: res.message,
                type: 'error',
                duration: 5 * 1000,
            });
        }
    }
}
</script>