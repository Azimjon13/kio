<template>
    <el-dialog title="Редактирование Тепловоза" width="70%" :visible.sync="this.show" :before-close="close">
        <div class="form-container">
            <el-form ref="editLocomotiveForm" :model="locomotive" label-position="top" >
                <el-row :gutter="20">
                    <el-col :span="6">
                        <el-form-item label="Наименование" >
                            <el-input v-model="locomotive.title"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Тип Тепловоза">
                            <el-select v-model="locomotive.id_group" disabled>
                                <el-option 
                                        v-for="group in locomotiveGroups"
                                        :key="group.id"
                                        :value="group.id"
                                        :label="group.name"
                                ></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Статус">
                            <el-select v-model="locomotive.status">
                                <el-option :value="1" label="Работает"></el-option>
                                <el-option :value="2" label="Списан"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Серийный №">
                            <el-input v-model="locomotive.serial_number" placeholder="Серийный №"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <hr>
                <h3 style="color:#5a04b9;">Перемещения Тепловоза</h3>
                <hr>
                <el-table 
                    ref="locomotivePlaces"
                    :data="locomotive.locomotive_places"
                    fit highlight-current-row border 
                    style="width: 100%;"
                >
                    <el-table-column  :min-width="5" label="№ п/п" align="center">
                        <template slot-scope="scope">
                            {{ scope.$index+1 }}
                        </template>
                    </el-table-column>
                    <el-table-column  :min-width="15" label="Подразделение">
                        <template slot-scope="scope">
                            <span v-if="scope.row.blocked">{{ scope.row.place.manager.title }}</span>
                            <el-select v-else v-model="scope.row.id_manager" @change="handleSelectManager($event, scope.row)">
                                <el-option v-for="manager in managers" :value="manager.id" :label="manager.title"></el-option>
                            </el-select>
                        </template>
                    </el-table-column>
                    <el-table-column  :min-width="15" label="Рудник">
                        <template slot-scope="scope">
                            <span v-if="scope.row.blocked">{{ scope.row.place.parent.title }}</span>
                            <el-select v-else v-model="scope.row.id_parent" @change="handleSelectRudnik($event, scope.row)">
                                <el-option v-for="rudnik in rudniks" :value="rudnik.id" :label="rudnik.title"></el-option>
                            </el-select>
                        </template>
                    </el-table-column>
                    <el-table-column  :min-width="15" label="Площадка">
                        <template slot-scope="scope">
                            <span v-if="scope.row.blocked">{{ scope.row.place.title }}</span>
                            <el-select v-else v-model="scope.row.id_place">
                                <el-option v-for="place in places" :value="place.id" :label="place.title"></el-option>
                            </el-select>
                        </template>
                    </el-table-column>
                    <el-table-column  :min-width="15" label="В период с:" align="center">
                        <template slot-scope="scope">
                            <span v-if="scope.row.blocked">{{ scope.row.date_from }}</span>
                            <el-date-picker 
                                v-else
                                value-format="yyyy-MM-dd" 
                                type="date" 
                                placeholder="Выберите дату"
                                v-model="scope.row.date_from" 
                                style="width: 100%;"
                                :pickerOptions = "{
                                    disabledDate(time) {
                                        if(scope.$index == 0) return true;
                                        const ind = scope.$index-1; 
                                        const disDate = locomotive.locomotive_places[ind].date_to;
                                        return time.getTime() < new Date(disDate).getTime();
                                    }
                                }"
                            ></el-date-picker>
                        </template>
                    </el-table-column>
                    <el-table-column  :min-width="15" label="В период до:" align="center">
                        <template slot-scope="scope">
                            <span v-if="!scope.row.flag || !scope.row.date_from">{{ scope.row.date_to }}</span>
                            <el-date-picker 
                                v-else
                                value-format="yyyy-MM-dd" 
                                type="date" 
                                placeholder="Выберите дату"
                                v-model="scope.row.date_to" 
                                style="width: 100%;"
                                :pickerOptions = "{
                                    disabledDate(time) {
                                        return time.getTime() < new Date(scope.row.date_from).getTime();
                                    }
                                }"
                            ></el-date-picker>
                        </template>
                    </el-table-column>
                    <el-table-column :min-width="8" align="center">
                        <template slot-scope="scope">
                            <el-button 
                                    v-if="scope.row.flag && scope.row.date_to" 
                                    type="success" 
                                    icon="el-icon-check" 
                                    circle
                                    @click="handleEndEditing(scope.row)"
                            ></el-button>
                            <el-button  
                                type="danger" 
                                icon="el-icon-delete" 
                                @click="removeLocomotivePlace(scope.row)"
                                circle
                            ></el-button>
                        </template>
                        <template slot="header" slot-scope="scope">
                                <el-button 
                                        v-if="showAddBtn" 
                                        type="success" 
                                        icon="el-icon-plus" 
                                        circle
                                        @click="addLocomotivePlace"
                                ></el-button>
                        </template>
                    </el-table-column>
                </el-table>
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
import {getLocomotive, updateLocomotive, deleteLocomotivePlace, getLocomotiveGroups} from "@/api/locomotives";
import {fetchPlaces} from "@/api/places";


export default { 
    props: {
        show: Boolean,
        close: Function, 
        locomotiveId: Number,
    },
    created() {
        this.loadTypes();
        this.loadManagers();
        this.loadLocomotive();
    },
    data() {
        return {
            locomotiveGroups:[],
            placesList: [],
            managers: [],
            rudniks: [],
            places: [],
        
            locomotive: {
                title: "",
                status: 1,
                serial_number: "",
                locomotive_places: [],
            },
        }
    },
    computed: {
        showAddBtn: function() {
            return this.locomotive.locomotive_places.filter(p=>p.flag).length == 0 && 
                   this.locomotive.locomotive_places.filter(p=> !(p.id_manager || p.id_parent || p.id_place || p.date_to)).length == 0;
            }
    },
    methods: {
        async loadTypes() {
            const {data} = await getLocomotiveGroups();
            this.locomotiveGroups = data;
        },
        async loadManagers() {
            const {data} = await fetchPlaces();
            this.placesList = data;
            this.managers = data.map(m => ({id:m.id, title:m.title}));
        },

        async loadLocomotive() {
            const {data} = await getLocomotive(this.locomotiveId);

            this.locomotive = {
                ...data, 
                locomotive_places:  data.locomotive_places.map(p => {
                    if(p.date_to) 
                        return {...p, blocked: true}; 
                    return {
                        ...p, 
                        flag: true, 
                        blocked: true,
                    };
                })
            };
        },

        handleSelectManager(managerId, row) {
            this.rudniks = this.placesList.find(m => m.id == managerId).places;
            row.id_parent = null;
            row.id_place = null;
        },
        handleSelectRudnik(rundikId, row) 
        {
            this.places = this.rudniks.find(r => r.id == rundikId).places;
            row.id_place = null;
        },

        
        async saveLocomotive() {
            const res = await updateLocomotive(this.locomotiveId, this.locomotive);
            console.log(res);
            if(res.status && res.errors == null)
            {
                this.$message({
                    message: 'Тепловоз успешно обновлен!',
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
        }, 

        handleEndEditing(row) {
            const fr = new Date(row.date_from).getTime();
            const to = new Date(row.date_to).getTime();
            if(fr >= to)
            {
                return this.$alert(`Поле в "Период до" должна быть больше поля "В период с:"`, `Неправильно указана дата!`);
            }
            
            if(!row.id)
            {
                const findedPlace = this.placesList.find(p => p.id == row.id_place); 
                const findedManager = this.managers.find(m => m.id == row.id_manager);
                row.place = {
                    id: findedPlace.id, 
                    id_manager: findedPlace.id_manager, 
                    title: findedPlace.title,
                    manager: { 
                        id: row.id_manager, 
                        title: findedManager.title,
                    },
                };
            }
            row.flag = false; 
            row.blocked = true;
        },

        addLocomotivePlace()
        {
            this.locomotive.locomotive_places.push({
                date_from: "",
                date_to: "",
                id_locomotive: this.locomotive.id, 
                id_place: null,
                id_parent: null,
                id_manager: null,
                flag: true,
                blocked: false,
            });
        }, 
        async removeLocomotivePlace(row) 
        {
            if(row.id) 
            {
                try {
                    const conf =  await this.$confirm("Вы действительно хотите удалить эту запись? Все связанные данные могут быть утеряны!", "Внимание", {type:"danger"});
                    if(conf != "confirm")
                        return;
                }
                catch(e)
                {
                    console.log(e);
                    return;
                }

                const res = await deleteLocomotivePlace(row.id);
                if(res.errors & !res.status) 
                {
                    this.$message({
                        message:res.message, 
                        type: "success",
                        duration: 5*1000,
                    });
                    return;
                }
                this.$message({
                    message:`Площадка дислокации Тепловоза успешно удалена!`, 
                    type: "success",
                    duration: 5*1000,
                });
            }
            const ind = this.locomotive.locomotive_places.indexOf(row);
            this.locomotive.locomotive_places.splice(ind, 1);
        } 
    }

}

</script>