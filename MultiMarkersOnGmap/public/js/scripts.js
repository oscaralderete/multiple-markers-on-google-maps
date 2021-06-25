/*
@author:Oscar Alderete <wordpress@oscaralderete.com>
@website: https://oscaralderete.com
@editor:NetBeans IDE v11.2
*/
var MultiMarkersOnGmap={bounds:null,map:null,infoWindow:null,markerIcon:{uri:pageData.marker_uri,width:32,height:40},initMap(){var x=document.getElementById("gmap");x.style.height=pageData.map_height+"px",this.map=new google.maps.Map(x,{zoom:11,center:new google.maps.LatLng(pageData.lat,pageData.lon),mapTypeId:google.maps.MapTypeId.ROADMAP}),this.bounds=new google.maps.LatLngBounds},placeMarkers(){var self=this;pageData.markers.forEach(obj=>{var lat=parseFloat(obj.latitude),lon=parseFloat(obj.longitude);self.bounds.extend(new google.maps.LatLng(lat,lon));var marker=new google.maps.Marker({position:new google.maps.LatLng(lat,lon),map:self.map,icon:new google.maps.MarkerImage(self.markerIcon.uri,new google.maps.Size(self.markerIcon.width,self.markerIcon.height),new google.maps.Point(0,0),new google.maps.Point(40,40))});self.infoWindow=new google.maps.InfoWindow,google.maps.event.addListener(marker,"click",function(){self.infoWindow.setContent(`<p class="map-info"><b>${obj.title}</b><br>${obj.content}</p>`),self.infoWindow.open(self.map,marker)})}),self.map.setCenter(self.bounds.getCenter()),self.map.fitBounds(self.bounds)}};MultiMarkersOnGmap.initMap(),MultiMarkersOnGmap.placeMarkers();