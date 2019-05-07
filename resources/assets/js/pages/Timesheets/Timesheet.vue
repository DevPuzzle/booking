<template>
<div>
    <div class="container">
        <div class="row">
            <div class="col-sm-3 mt-2">
                <h3>Add work time</h3> 
                <hr>
                <form>
                    <div class="form-group">
                        <label for="picker">Select date :</label>
                        <date-picker :config="timePickerOption" id="picker" v-model="selectedDay"></date-picker>
                    </div>

                    <div class="form-group">
                        <label for="elapsed_time">Elapsed time :</label>
                        <input type="number" v-model="elapsed_time" class="form-control" step="0.5"/>
<!--                        <select class="custom-select" name="elapsed_time" id="elapsed_time" v-model="elapsed_time">
                            <option value="30">30m</option>
                            <option value="60">1h</option>
                            <option value="90">1h 30m</option>
                            <option value="120">2h</option>
                            <option value="150">2h 30m</option>
                            <option value="180">3h</option>
                            <option value="210">3h 30m</option>
                            <option value="240">4h</option>
                            <option value="270">4h 30m</option>
                            <option value="300">5h</option>
                            <option value="330">5h 30m</option>
                            <option value="360">6h</option>
                            <option value="390">6h 30m</option>
                            <option value="420">7h</option>
                            <option value="450">7h 30m</option>
                            <option value="480">8h</option>
                            <option value="510">8h 30m</option>
                        </select>-->
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes :</label>
                        <input type="textarea" class="form-control" name="notes" id="notes" v-model="notes"/>
                    </div>
                    
                    <a href="#" class="btn btn-block btn-primary" @click="addToTimeSheet()">Add time</a>
                </form>
            </div>
            <div class="col-sm-9 mt-2">
                <h3 class="">My time</h3>
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
                
                <div class="row mt-3">

                    <table class="table table-bordered smallFont">
                        <thead>
                            <tr>
                                <th scope="col" class="promlead-th-bg">Day</th>
                                <th scope="col" class="promlead-th-bg">Notes</th>
                                <th scope="col" class="promlead-th-bg">Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr v-for="x in 7" :class="{'text-muted': referenceDate.clone().day(x - 1).isAfter(today)}">
                            <th scope="row">
                                {{ referenceDate.clone().day(x - 1).format('ddd, Do MMM') }}
                            </th>
                            <td >
                                
                                <div class="row mt-1" v-for="item in getDayElapsetTime( referenceDate.clone().day(x-1) )">
                                    <div class="col-md-2">
                                        <span class="icon-close pl-2 my-auto" style="cursor: pointer;color: red"
                                          @click="deleteTime(item)"></span>
                                    </div>
                                    <div class="col-md-10">
                                        <span class="d-block"> <b>{{item.notes}}</b> </span>
                                        <span> Elapset time :<b> {{ getFormatedTime(item.hours) }} </b> </span>
                                    </div>
                                    
                                </div> 
                              
                                       
                            </td>
                            <td>
                                
                                {{ getHours(referenceDate.clone().day(x-1)) }}
                            </td>
                        </tr>
                        
                            
                        
                        </tbody>
                    </table>
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
            deleteTime(item){
                
                let indexdayItem = _.findIndex(this.timesheet,{'date': item.date,'hours':item.hours });
                this.deleteTimeSeeht(item);
                this.timesheet.splice(indexdayItem,1);
                
                // minus elapset time
                let slot = _.find(this.timesheetData, {'date': item.date});
                slot.hours =Number(slot.hours) - Number(item.hours);
            },
            deleteTimeSeeht(item){
                axios.post('api/deletetimesheets', item )
                    .then(() => {
                        this.$notify({
                            group: 'notifications',
                            type: 'success',
                            text: 'Timesheet Successfully Deleted!'
                        });
                        //this.timesheet = [];
                    }, (err) => {
                        this.$notify({
                            group: 'notifications',
                            type: 'error',
                            text: 'Deleted Failed! Try Again!'
                        });
                    });
            },
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
                
                return  _.filter(this.timesheet, {'date': formated}) ;
                //return  _.find(this.timesheet, {'date': formated});
            },
            
            getFormatedTime(hours){
                
                let answer =''; 
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
                    let slot = _.find(this.timesheetData, {'date': formated});
                    if( slot !== undefined ){
                        
                        total = Number(total) + Number (slot.hours);
                        
                    }
                }
                
                return this.getFormatedTime(total);
                
            },
            getHours(date) {
                let formated = date.format('YYYY-MM-DD');
                let slot = _.find(this.timesheetData, {'date': formated});
                let answer =''; 
                
                if (slot !== undefined){
                    
                    answer = this.getFormatedTime(slot.hours);
                    
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
                axios.get(`api/timesheets?start_date=${this.referenceDate.clone().day(0).format('YYYY-MM-DD')}&end_date=${this.referenceDate.clone().day(6).format('YYYY-MM-DD')}`)
                    .then((resp) => {
                        this.timesheet = [];
                        this.timesheetData = [];
                        let data = resp.data;
                        data.map((slot) => {
                            let formatedDate = moment(slot.date).format('YYYY-MM-DD');
                            //this.timesheetData = _.concat(this.timesheetData, {date: formatedDate, hours: slot.hours});
                            
                            
                            this.timesheet=_.concat(this.timesheet, {date: formatedDate, hours: slot.hours , id:slot.id, notes:slot.notes});
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
            },
            saveTimesheet() {
                //if (!this.hasChanged()) return;
                axios.post('api/timesheets', this.timesheet)
                    .then(() => {
                        this.$notify({
                            group: 'notifications',
                            type: 'success',
                            text: 'Timesheet Successfully Saved!'
                        });
                        //this.timesheet = [];
                    }, (err) => {
                        this.$notify({
                            group: 'notifications',
                            type: 'error',
                            text: 'Save Failed! Try Again!'
                        });
                    });
            },

        },
    }
</script>