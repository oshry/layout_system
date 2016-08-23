/**
 * Created by oshry on 19/08/2016.
 */
const BASE = location.protocol+'//'+location.hostname+(location.port ? ':'+location.port: '')+'/tests/2/admin/api/';
class Admin{
    constructor(){
        let _this = self;
        this.modules ='';
        this.init(_this);
    }
    //modules list promise
    ajax_modules(url){
        return new Promise((resolve, reject) => {
            $.ajax({
                type:"GET",
                url: url,
                dataType: "json",
                success: function(xhr, status, error){
                    resolve(xhr);
                },
                error: function(req){
                    reject(new Error(req.statusText));
                }
            });
        });
    }
    modules_storage(response){
        new Promise((resolve, reject)=>{
            this.modules = response;
            resolve(response);
        });
        
    }
    load_modules_list(){
        this.ajax_modules(BASE+"modules_list/")
            .then((x) => this.modules_storage(x))
            .catch(err => console.log(err));
    }
    init(e){
        let blat = this;
        blat.load_modules_list();
        //on page layout change -> load areas forms in order to decide which module will load in which place
        // $('.select-layout').on('change', function(b){
        //     //console.log(blat.modules);
        //     //console.log(this.value);
        //     let layout_id = this.value;
        //     let parts = $('option:selected', this).attr('modules');
        //     // console.log(parts);
        //     $('#modules').html('');
        //     let modules_list = '';
        //     let what = blat.modules['modules_list'];
        //     what.forEach(child =>{
        //         modules_list+=`<option value="`+child.id+`">`+child.name+`</option>`;
        //     });
        //     //load areas forms
        //     for (var index = 0; index < parts; index++) {
        //         $('#modules').append(`
        //         <form class="area_form` + index + `" method="post" onsubmit="event.preventDefault();">
        //             <input type="hidden" id="layout_areas" name="layout_areas" value="` + layout_id + `"/>
        //             <div class="line">
        //                 <label for="layout"> Area ` + index + `: Select Module</label>
        //                 <select name="select-module" class="select-module` + index + `">
        //                     <option>  </option>` +
        //                     modules_list
        //                     +
        //                     `</select>
        //             </div>
        //             <div class="line">
        //                 <div class="controls">
        //                 <input type="submit" name="submit" class="button" id="submit_btn" value="Submit" />
        //                 </div>
        //             </div>
        //             </form>
        //             <div class="configuration` + index + `"></div>
        //             <script type="application/javascript" charset="UTF-8">
        //                 //submit area
        //                 $('.area_form` + index + `').on('submit', function(){
        //                      let form_data = $(this).serialize();
        //                      let form_arr = $(this).serializeArray();
        //                      let module_id = form_arr[1]['value'];
        //                      let _this = this;
        //                      //+e.target.value
        //                     $.ajax({
        //                         type: "POST",
        //                         url:"`+BASE+`insert_area",
        //                         data: form_data,
        //                         success:function(area_id){
        //                             $(_this).find('input[type=submit]').prop('disabled', true);
        //                             //show module configuration form
        //                             $.ajax({
        //                                 type:"GET",
        //                                 url: '`+BASE+`load_configuration_form/'+module_id+'/'+area_id,
        //                                 success:function(data){
        //                                     $('.configuration` + index + `').html();
        //                                     $('.configuration` + index + `').append(data);
        //                                 },
        //                                 error:function(){
        //
        //                                 }
        //                             });
        //                             console.log('success');
        //                         },
        //                         error: function(){
        //                             console.log('error');
        //                         }
        //                     });
        //                 });
        //             </script>
        //             `);
        //     }
        //
        // });
        $('#create_page').on('submit', function(e){
            e.preventDefault();
            let data = $(this).serialize();
            let data_arr = $(this).serializeArray();
            console.log(data_arr);
            let _this = this;
            $.ajax({
                url:BASE+"create_page",
                type:"POST",
                data: data,
                success:function(data){
                    $(_this).find('input[type=submit]').prop('disabled', true);
                    let layout_id = data_arr[2]['value'];
                    //console.log(data);
                    let parts = $('option:selected', _this).attr('modules');
                    $('#modules').html('');
                    let modules_list = '';
                    let what = blat.modules['modules_list'];
                    what.forEach(child =>{
                        modules_list+=`<option value="`+child.id+`">`+child.name+`</option>`;
                    });
                    //load areas forms
                    for (var index = 0; index < parts; index++) {
                        $('#modules').append(`
                            <form class="area_form` + index + `" method="post" onsubmit="event.preventDefault();">
                                <input type="hidden" id="layout_areas" name="layout_areas" value="` + layout_id + `"/>
                                <input type="hidden" id="page_id" name="page_id" value="` + data + `"/>
                                <div class="line">
                                    <label for="layout"> Area ` + index + `: Select Module</label>
                                    <select name="select-module" class="select-module` + index + `">
                                        <option>  </option>` +
                                        modules_list
                                        +
                                        `</select>
                                </div>
                                <div class="line">
                                    <div class="controls">
                                    <input type="submit" name="submit" class="button" id="submit_btn" value="Submit" />
                                    </div>
                                </div>
                                </form>
                                <div class="configuration` + index + `"></div>
                                <script type="application/javascript" charset="UTF-8">
                                    //submit area
                                    $('.area_form` + index + `').on('submit', function(){
                                         let form_data = $(this).serialize();
                                         let form_arr = $(this).serializeArray();
                                         let module_id = form_arr[2]['value'];
                                         let _this = this;
                                         //+e.target.value
                                        $.ajax({
                                            type: "POST",
                                            url:"`+BASE+`insert_area",
                                            data: form_data,
                                            success:function(area_id){
                                                $(_this).find('input[type=submit]').prop('disabled', true);
                                                //show module configuration form 
                                                $.ajax({
                                                    type:"GET",
                                                    url: '`+BASE+`load_configuration_form/'+module_id+'/'+area_id,
                                                    success:function(data){
                                                        $('.configuration` + index + `').html();
                                                        $('.configuration` + index + `').append(data);
                                                    },
                                                    error:function(){
                                                    
                                                    }
                                                });
                                                console.log('success');
                                            },
                                            error: function(){
                                                console.log('error');
                                            }
                                        });
                                    });
                                </script>
                                `);
                                }
                },
                error:function(){
                    console.log('nooo');
                }
            });
            console.log('yeahhhhh');
        });
    }
}
export default Admin;