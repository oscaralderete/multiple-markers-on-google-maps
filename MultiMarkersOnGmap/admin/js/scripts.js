/*
@author:Oscar Alderete <wordpress@oscaralderete.com>
@website: https://oscaralderete.com
@editor:NetBeans IDE v11.2
*/
const MultiMarkersOnGmap=Vue.createApp({data(){return{key:pageData.key,markers:pageData.markers,marker:this.getBlankMarker(),map_height:pageData.map_height}},components:{"oa-toast":OA_Toast,"oa-dialogs":OA_Dialogs},methods:{getBlankMarker(){return{title:"",content:"",latitude:null,longitude:null}},updateApiKey(){var self=this;if(""===this.key.trim())return this.$refs.dialogs.show({title:"ERROR",message:"You must enter your Google maps API key"}),!1;this.loader(1),this.fetch({type:"save_api_key",data:{key:self.key}},function(response){self.loader(0),self.$refs.toast.text({message:response.msg,type:"success"})})},updateMapHeight(){var self=this;if(this.map_height<=0)return this.$refs.dialogs.show({title:"ERROR",message:"You must enter a valid height"}),!1;this.loader(1),this.fetch({type:"save_map_height",data:{map_height:self.map_height}},function(response){self.loader(0),self.$refs.toast.text({message:response.msg,type:"success"})})},saveMarker(){event.preventDefault();const self=this;this.markers.push(this.marker),this.loader(1),this.fetch({type:"save_marker",data:{markers:self.markers,action:"save"}},function(response){self.loader(0),self.marker=self.getBlankMarker(),self.toggleAddModal(),self.$refs.toast.text({message:response.msg,type:"success"})})},toggleAddModal(){const f=document.getElementById("formAdd");f.classList.toggle("active")},cancelAddModal(){event.preventDefault(),this.toggleAddModal()},deleteMarker(i,obj){const self=this;this.$refs.dialogs.show({title:"WARNING",message:"Are you sure you want to delete this marker: "+obj.title+"?",buttons:{ok:{callback:function(){self.loader(1),self.markers.splice(i,1),self.fetch({type:"save_marker",data:{markers:self.markers,action:"delete"}},function(response){self.loader(0),self.$refs.toast.text({message:response.msg,type:"success"})})}},cancel:{label:"CANCEL"}}})},fetch(obj,callback){var self=this;jQuery.ajax({url:ajaxurl,type:"POST",data:{action:pageData.ajax_action,type:obj.type,data:obj.data},success:function(x){x=JSON.parse(x);"function"==typeof callback&&"OK"===x.result?callback(x):(self.loader(0),self.$refs.dialogs.show({title:"ERROR",message:x.msg}))},error:function(error){self.loader(0),self.$refs.dialogs.show({title:"ERROR",message:"Request error code: "+error.status})}})},loader(num){const x=document.getElementById("loader").classList;var y="active";1===num?x.add(y):x.remove(y)}}}).mount("#MultiMarkersOnGmap");