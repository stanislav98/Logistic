<template>
	<div class="maps">
		 <gmap-map
	      ref="gmap"
	      :center="center"
	      :zoom="10"
	      :options="{minZoom: 2, maxZoom: 12}"
	      style="width:100%;  height: 65vh; margin-bottom: 60px"
	    >
	      <gmap-marker
	        :key="index"
	        v-for="(m, index) in this.tovari"
	        :position="google && new google.maps.LatLng(m.to_lat,m.to_lng)"
	        @click="toggleInfoWindow(m,index)">
	        {{ google && new google.maps.LatLng(m.to_lat,m.to_lng)}}
	      </gmap-marker>
	      <gmap-info-window
	        :options="infoOptions"
	        :position="infoWindowPos"
	        :opened="infoWinOpen"
	        @closeclick="infoWinOpen=false"
	      >
	        <div v-html="infoContent"></div>
	      </gmap-info-window>

    </gmap-map>
	</div>
</template>

<script>
  import {gmapApi} from 'vue2-google-maps';

  export default {
    name: "GoogleMap",
    computed: {
    	tovari () {
	    	return this.$store.state.tovari
	    },
	    google: gmapApi,
    },
    data() {
      return {
        //a default center for the map
        center: {lat: '', lng: ''},
        map: null,
        infoContent: '',
        infoWindowPos: {
          lat: 0,
          lng: 0
        },
        infoWinOpen: false,
        currentMidx: null,
        infoOptions: {
          pixelOffset: {
            width: 0,
            height: -35
          }
        },
      };
    },
    created() {
    	 const success = (position) => {
        	this.center.lat  = position.coords.latitude;
        	this.center.lng = position.coords.longitude;
        };
        const error = (err) => {
        	this.center.lat = parseFloat(42.698334)
        	this.center.lng = parseFloat(23.319941)
        };

        navigator.geolocation.getCurrentPosition(success, error);
    },
    mounted() {
   
    },
    methods: {
      toggleInfoWindow: function (marker, idx) {
      	console.log(marker);
		var service = new google.maps.DistanceMatrixService();
		let start = google && new google.maps.LatLng(marker.to_lat,marker.to_lng)
		let end = google && new google.maps.LatLng(marker.from_lat,marker.to_lng)
		service.getDistanceMatrix({
		    origins: [start],
		    destinations: [end],
		    travelMode: 'DRIVING',
		    unitSystem: google.maps.UnitSystem.metric,
		    avoidHighways: false,
		    avoidTolls: false,
		}, function(response, status) {
		  	if (status == 'OK') {
		  		console.log(response)
		  		//Distance 
		  		console.log(response.rows[0].elements[0].distance.text)

		  		//Kilometers 
		  		console.log(response.rows[0].elements[0].duration.text)
			}
		});
        this.infoWindowPos = google && new google.maps.LatLng(marker.to_lat,marker.to_lng);
        this.infoContent = this.getInfoWindowContent(marker);
        if (this.currentMidx == idx) {
          this.infoWinOpen = !this.infoWinOpen;
        }
        else {
          this.infoWinOpen = true;
          this.currentMidx = idx;
        }
      },
        getInfoWindowContent: function (marker) {
        return (`<article class="single_tovar inside_modal">
		<div class="heading">
			<div class="left_heading">
				<h3>От ${marker.from_city} до ${marker.to_city}</h3>
				<div class="tooltip"  v-if="marker.fast_payment == 1">
					<span class="material-icons">payments</span>
					<span class="tooltiptext">Бързо плащане</span>
				</div>
				<div class="tooltip"   v-if="marker.adr == 1">
					<img src="../images/adr.png" alt="ADR">
					<span class="tooltiptext">ADR - Опасен товар</span>
				</div>
			</div>
			<p class="price">${marker.price}лв</p>
		</div>
		<div class="row">
			<p class="wrap_icon_text">
				<span class="material-icons">business</span>
				<span>${marker.firmName}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">today</span>
				<span>${marker.from_date}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">event_busy</span>
				<span>${marker.to_date}</span>
			</p>
		</div>
		<div class="row">
			<p class="wrap_icon_text">
				<span class="material-icons">local_shipping</span>
				<span>${marker.vehicleName}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">add_road</span>
				<span>500.00 км.</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">schedule</span>
				<span>1час 30мин</span>
			</p>
		</div>
		<p v-if="marker.description" class="desc">${marker.description}</p>
		<a href="#" class="btn flex_btn">
			<span class="material-icons">forward_to_inbox</span>Свържи се сега
		</a>
	</article>`
		);
	   },
	}
};
</script>
