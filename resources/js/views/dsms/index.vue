<template>
    <div class="app-container">
        <h3 style="text-align:center;">
            Показатели по ДСМ по {{ manager.title }}
            <span style="float: right;">
                на дату:
                <el-date-picker
                    v-model="date"
                    value-format="yyyy-MM-dd"
                    type="date"
                    format="dd.MM.yyyy"
                    :clearable="false"

                    @change="dateChanged"
                    :picker-options="{
                        shortcuts: [{
                                        text: 'Сегодня',
                                        onClick(picker) {
                                        picker.$emit('pick', new Date());
                                        }
                                    }]
                    }"
                ></el-date-picker>
            </span>
        </h3>

        <el-radio-group class="radio-buttons" v-model="currentSelectedRudnik" style="margin-bottom: 30px" @change="changeRudnik">
            <el-radio-button
                v-for="rudnik in rudniks"
                :label="rudnik.id"
            >
                {{ rudnik.title }}
            </el-radio-button>
        </el-radio-group>
        <el-tabs  type="border-card">
            <el-tab-pane
                v-for="place in currentPlaces"
                :label="place.title"
            >
                <MainTable :id_place="place.id" :date="date" :updater="updater"></MainTable>
                <Downtimes :id_place="place.id" :date="date" :updater="updateMain"></Downtimes>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>


<script>
//import {ref} from 'vue';
import Downtimes from "./components/Downtimes.vue";
import MainTable from "./components/MainTable.vue";
import {getManager} from "@/api/places";

export default {
    created() {
        this.loadData();
    },
    data() {
        return {
            updater: 0,
            manager: {
                title: "",
                id: this.$route.meta && this.$route.meta.managerId
            },
            rudniks: [],
            currentPlaces: [],
            currentSelectedRudnik: null,

            date: sessionStorage.getItem('date') || this.getPreviousDate(),
        }
    },
    methods: {
        updateMain()
        {
            this.updater = this.updater+1;
        },
        async loadData()
        {
            sessionStorage.setItem('date', this.date);
            const {data} = await getManager(this.manager.id, '/dsm');
            this.manager = {
                id: data.id,
                title: data.title,
            };

            this.rudniks = data.places;
            this.currentSelectedRudnik = data.places[0].id;
            this.currentPlaces = data.places[0].places;
        },
        changeRudnik(rudnikId)
        {
            this.currentPlaces = this.rudniks.find(p => p.id == rudnikId).places;
        },
        dateChanged(date)
        {
            sessionStorage.setItem(`date`, this.date);
        }, 
        getPreviousDate()
        {
            const previous = new Date();
            previous.setDate(previous.getDate() - 1);
            const d = new Date(previous).toISOString().slice(0,10);
            return d;
        }
    },
    components: { Downtimes, MainTable },
}

</script>

<style scoped>
.radio-buttons >>> .el-radio-button__inner {
  border-radius: 4px;
  outline: none;
  margin-right: 10px;
  margin-bottom: 5px;
  border-left: 1px solid #DCDFE6;
  box-shadow: 0 0 0 0 #409EFF;
  width: 15rem;
}

.radio-buttons >>> .el-radio-button:last-child .el-radio-button__inner {
  margin-right: 0;
}
</style>
