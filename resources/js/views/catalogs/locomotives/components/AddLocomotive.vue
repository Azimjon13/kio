<template>
    <el-dialog title="Добавление Автосамосвала" width="40%" :visible.sync="this.show" :before-close="close">
        <div class="form-container">
            <el-form ref="addLocomotiveForm" :model="locomotive" label-position="top" >
                <el-descriptions :column="1" border>
                    <el-descriptions-item >
                        <template slot="label">
                           Наименование
                        </template>
                        <el-input v-model="locomotive.title"></el-input>
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                           Тип Тепловоза
                        </template>
                        <el-select v-model="locomotive.id_group">
                            <el-option 
                                v-for="group in locomotiveGroups" 
                                :value="group.id" 
                                :label="group.name"
                            ></el-option>
                        </el-select>
                    </el-descriptions-item>
                    <el-descriptions-item >
                        <template slot="label">
                            Серийный №
                        </template>
                        <el-input v-model="locomotive.serial_number"></el-input>
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                           Подразделение
                        </template>
                        <el-select v-model="locomotive.id_division" @change="handleSelectManager">
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
                        <el-select v-model="locomotive.id_rudnik" @change="handleSelectRudnik">
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
                        <el-select v-model="locomotive.id_place">
                            <el-option 
                                v-for="place in places" 
                                :value="place.id"
                                :label="place.title"
                            ></el-option>
                        </el-select>
                    </el-descriptions-item>
                    <el-descriptions-item >
                        <template slot="label">
                            Приступил к работе с:
                        </template>
                        <el-date-picker
                                value-format="yyyy-MM-dd"
                                v-model="locomotive.date_from"
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
                <el-button type="primary" @click="saveLocomotive">
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
import {storeLocomotive, getLocomotiveGroups} from "@/api/locomotives";
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
            locomotiveGroups: [],
            placesList: [],
            managers: [],
            rudniks: [],
            places: [],
            locomotive: {
                title: "",
                id_division: null, 
                id_rudnik: null,
                id_place: null,
                serial_number: "",
                date_from: "",
                status: 1,
            },
        }
    },
    methods: {
        async loadTypes() {
            const {data} = await getLocomotiveGroups();
            this.locomotiveGroups = data;
        },
    
        async loadManagers() {
            const {data} = await fetchPlaces();
            this.placesList = data.flatMap(m=>m.places);
            this.managers = data.map(m => ({id:m.id, title:m.title}));
            console.log(this.placesList);
        },

        handleSelectManager(managerId) {
            this.rudniks = this.placesList.filter(p=>p.id_manager == managerId); ;
            this.locomotive.id_place = null;
        },

        handleSelectRudnik(rundikId)
        {
            this.places = this.rudniks.find(r => r.id == rundikId).places;
            this.locomotive.id_place = null;
        }, 

        async saveLocomotive() {
            const res = await storeLocomotive(this.locomotive);
            console.log(res);
            if(res.status && res.errors == null)
            {
                this.$message({
                    message: 'Тепловоз успешно добавлен!',
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