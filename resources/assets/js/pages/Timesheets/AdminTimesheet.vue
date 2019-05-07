<template>
<div>
    <div class="container">
        <div class="row">
            <div class="col-sm-3 mt-2">
                <h3>Filter</h3> 
                <hr>
                <div class="form-group">
                            <label  class="form-label">Users :</label>
                            <select class="form-control" v-model="sectedUserFilter">
                            <option value="all">All</option>
                            <option v-for="user in usersList" v-bind:value="user.user_id">
                                {{ user.username }}
                            </option>
                            </select>
                </div>
            </div>
            <div class="col-sm-9 mt-2">
                <h3 class="">Users timesheets</h3>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                    <button class="btn btn-block btn-secondary mr-auto mb-2" @click="resetDates()">Current week</button>
                    </div>
                    <div class="col-md-9 d-flex justify-content-end align-items-center mb-2">
                        <span class="icon-arrow-left-circle mr-2 " @click="previousWeek()"
                              style="cursor: pointer;"></span>
                        <input v-model="currentWeek" type="text" class="form-control col-md-10 smallFont"
                               placeholder="Current Week" />
                        <span class="icon-arrow-right-circle ml-2 " @click="nextWeek()" style="cursor: pointer;"></span>
                    </div>
                </div>
                
                <div class="container mt-3 es_smallFont">
                                       
                    <div class="row container-fluid promlead-th-bg py-3 font-weight-bold">
                        <div class="col-3 ">
                             Day
                        </div>
                        <div class="col-7">
                            <div class="row">
                                <div  class="col-2 promlead-th-bg pl-0">User</div>
                                <div  class="col-6 promlead-th-bg ml-2">Notes</div>
                               
                            </div>
                        </div> 
                         <div  class="col-2 promlead-th-bg">Day hours</div>
                    </div>
                    <div class="row container-fluid border-bottom pt-2 " v-for="x in 7" :class="{'text-muted': referenceDate.clone().day(x - 1).isAfter(today)}">
                         
                            <div class="col-3">
                                {{ referenceDate.clone().day(x - 1).format('ddd, Do MMM') }}
                            </div>
                            <div class="col-7">
                                 <div class="row" v-for="item in getDayElapsetTime( referenceDate.clone().day(x-1) )">
                                     <div class="col-2 align-top pl-1">{{item.username}}</div>
                                    <div class="col-9 px-0">
                                        <span class="d-block ml-2"> <b>{{item.notes}}</b> </span>
                                        <span class="ml-2"> Elapset time :<b> {{ getFormatedTime(item.hours) }} </b> </span>
                                    </div> 
                                 </div>
                            </div>
                            <div class="col-2 pr-0">
                                        {{ getHours(referenceDate.clone().day(x-1)) }}
                            </div> 
                        
                    </div>
                    <div class="col-12 text-right">Week total : <b>{{getTotalWeekHours()}}</b></div>
                  
                    
                </div>
                
            </div>
        </div>
    </div>

</div>
</template>
<script>
    //import Editor from '../../components/Toggleable.vue'

    export default {
        components: {
            //Editor
        },
        computed: {
            weekStart() {
                return this.referenceDate.clone().day(0);
            },
            weekEnd() {
                return this.referenceDate.clone().day(6);
            },
            today() {
                return moment()
            },
            timePickerOption() {
                return {
                  format: 'MM/DD/YYYY' ,
                }
            }
           
            
            
        },
        created() {
            this.referenceDate = moment();
            this.selectedDay = moment();
            this.elapsed_time = 0.5;
            this.refresh();
        },
        data() {
            return {
                sectedUserFilter : 'all',
                usersList:[],
                elapsed_time : null,
                selectedDay : null,
                notes : '',
                referenceDate: null,
                timesheet: [],
                timesheetData: [],
                currentWeek: null
            }
        },
        methods: {
                        
            addToTimeSheet(){
                const formated = moment(this.selectedDay).format('YYYY-MM-DD');
                
                //this.timesheet = _.concat(this.timesheet, {date: formated, hours: this.elapsed_time/60});
                //this.timesheetData = _.concat(this.timesheetData, {date: formated, hours: this.elapsed_time/60});
                
                this.dayInput(this.elapsed_time,moment(this.selectedDay),this.notes);
                //this.$forceUpdate();
                this.saveTimesheet();
                this.refresh();
            },
            
            dayInput(val, date, notes) {
                const formated = date.format('YYYY-MM-DD');
                this.timesheet = _.concat(this.timesheet, {date: formated, hours: val , notes : notes});
                let slot = _.find(this.timesheetData, {'date': formated});
                if (slot === undefined) {
                    this.timesheetData = _.concat(this.timesheetData, {date: formated, hours: val});

                } else {
                    slot.hours =Number(slot.hours) + Number(val);
                }
            },
            
            getDayElapsetTime(date){
                let formated = date.format('YYYY-MM-DD');
                
                const dayFilterder = _.filter(this.timesheet, {'date': formated});
                const userFiltered = _.filter(dayFilterder, item => {
                            if( this.sectedUserFilter == 'all' ){
                                return true;
                            }
                            return item.user_id == this.sectedUserFilter;
                        });
                
                return   userFiltered;
                //return  _.find(this.timesheet, {'date': formated});
            },
            
            getFormatedTime(hours){
                
                let answer = ''; 
                if (hours !== undefined){
                    answer = Math.floor(hours)+'h '+(hours-Math.floor(hours))*60+'m';
                }
                else {
                    answer='-';
                }
                return answer; 
                
            },
            getTotalWeekHours(){
                let total=0;
                
                for (var i = 1; i <= 7; i++) {
                    
                    
                    let formated = this.referenceDate.day(i-1).format('YYYY-MM-DD');
                    let dayFilterder = _.filter(this.timesheet, {'date': formated});
                    let userFiltered = _.filter(dayFilterder, item => {
                            if( this.sectedUserFilter == 'all' ){
                                return true;
                            }
                            return item.user_id == this.sectedUserFilter;
                        });
                    let sum = _.reduce(userFiltered, function (memo, item) {
                                return memo + item.hours;
                                }, 0);
                    
                    total = Number(total)+ Number(sum);
                }
                
                return this.getFormatedTime(total);
                
            },
            getHours(date) {
                let formated = date.format('YYYY-MM-DD');
                //let slot = _.find(this.timesheetData, {'date': formated});
                
                let dayFilterder = _.filter(this.timesheet, {'date': formated});
                let userFiltered = _.filter(dayFilterder, item => {
                            if( this.sectedUserFilter == 'all' ){
                                return true;
                            }
                            return item.user_id == this.sectedUserFilter;
                        });
                        
                let sum = _.reduce(userFiltered, function (memo, item) {
                    return memo + item.hours;
                }, 0);
                
                let answer =''; 
                
                if (sum > 0 ){
                    
                    answer = this.getFormatedTime(sum);
                    
                }
                else {
                    answer='-';
                }
                return answer;    
                //return slot === undefined ? '0' : slot.hours;
            },
            hasChanged() {
                return this.timesheet.length !== 0;
            },
            nextWeek() {
                this.referenceDate.add(1, 'week');
                this.refresh();
            },
            previousWeek() {
                this.referenceDate.subtract(1, 'week');
                this.refresh();
            },
            refresh() {
                
                this.currentWeek = this.referenceDate.clone().day(0).format('ddd, Do MMM gggg') + ' to ' + this.referenceDate.clone().day(6).format('ddd, Do MMM gggg');
                axios.get(`api/admintimesheets?start_date=${this.referenceDate.clone().day(0).format('YYYY-MM-DD')}&end_date=${this.referenceDate.clone().day(6).format('YYYY-MM-DD')}`)
                    .then((resp) => {
                        this.timesheet = [];
                        this.timesheetData = [];
                        let data = resp.data;
                        data.map((slot) => {
                            let formatedDate = moment(slot.date).format('YYYY-MM-DD');
                            //this.timesheetData = _.concat(this.timesheetData, {date: formatedDate, hours: slot.hours});
                            
                            
                            this.timesheet=_.concat(this.timesheet, {date: formatedDate, hours: slot.hours , id:slot.id, notes:slot.notes , user_id : slot.user.id,username:slot.user.username});
                            
                            this.usersList=_.unionBy(this.timesheet,
                                function(timesheet){ return timesheet.user_id;});
                            
                            let dslot = _.find(this.timesheetData, {'date': formatedDate});
                            if (dslot === undefined) {
                                this.timesheetData = _.concat(this.timesheetData, {date: formatedDate, hours: slot.hours});

                                } else {
                                    dslot.hours = Number(dslot.hours) + Number(slot.hours);
                                }
                                })
                            });
                this.$forceUpdate();
            },
            resetDates() {
                this.referenceDate = moment();
                this.refresh();
            }
            

        },
    }
</script>