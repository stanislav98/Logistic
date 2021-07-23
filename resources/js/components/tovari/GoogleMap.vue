<template>
	<div class="maps" v-if="this.tovari.length !== 0">
		<gmap-map
	      ref="gmap"
	      :center="center"
	      :zoom="10"
	      :options="{minZoom: 2, maxZoom: 50}"
	      style="width:100%;  height: 65vh; margin-bottom: 60px"
	    >
	    <GmapCluster @click="toggleClusterInfo">
			<gmap-marker
		        :key="index"
		        v-for="(m, index) in this.tovari"
		        :title="m.id.toString()"
		        :position="{
		        	lat:parseFloat(m.to_lat),
		        	lng: parseFloat(m.to_lng)
		        }"
		        :zoom-on-click="true"
		        @click="toggleInfoWindow(m,index)"
			/>
		</GmapCluster>
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
  import GmapCluster from 'vue2-google-maps/src/components/cluster'

  export default {
    name: "GoogleMap",
    components: {
    	'GmapCluster': GmapCluster
    },
    computed: {
    	user() {
    		return this.$store.state.user
    	}
    },
    props: {
    	tovari: {
    		type: Array 
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
        	this.center.lat  = parseFloat(position.coords.latitude);
        	this.center.lng = parseFloat(position.coords.longitude);
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
		toggleClusterInfo(cluster) {
		    cluster.markers_.forEach((val,idx) => {
		    	let index = this.tovari.findIndex(o => o.id == val.title)
		    	this.toggleInfoWindow(this.tovari[index], index)
		    })
		 },
		getCoords(lat,lng) {
			return {
				lat:parseFloat(lat),
				lng:parseFloat(lng)
			}
		},
		toChat(id) {
	    	this.$store.commit('setActiveFriend',id)
	    	this.$router.push({ path: `/dashboard/chat` })
	    },
		toggleInfoWindow: function (marker, idx) {
			let vueThis = this
			if(marker.distance === '0 км' || marker.time === '0 часа') {
		  	let vm = marker
			var service = new google.maps.DistanceMatrixService();
			let start = this.getCoords(marker.to_lat,marker.to_lng)
			let end = this.getCoords(marker.from_lat,marker.from_lng)
			
			service.getDistanceMatrix({
			    origins: [start],
			    destinations: [end],
			    travelMode: 'DRIVING',
			    unitSystem: google.maps.UnitSystem.metric,
			    avoidHighways: false,
			    avoidTolls: false,
			}, function(response, status) {
			  	if (status == 'OK') {
			  		if (status == 'OK') {
				  		let obj = response.rows[0].elements[0]
				  		if(obj.distance) {
				  			vm.distance = obj.distance.text
				  		}
				  		if(obj.duration) {
				  			vm.time = obj.duration.text
				  		}
					}
				}
			})
			.then(function() {
		        vueThis.infoWindowPos = vueThis.getCoords(marker.to_lat,marker.to_lng);
		        vueThis.infoContent = vueThis.getInfoWindowContent(marker);
		        if (vueThis.currentMidx == idx) {
		          vueThis.infoWinOpen = !vueThis.infoWinOpen;
		        }
		        else {
		          vueThis.infoWinOpen = true;
		          vueThis.currentMidx = idx;
		        }
			});
		} else {
			vueThis.infoWindowPos = vueThis.getCoords(marker.to_lat,marker.to_lng);
		    vueThis.infoContent = vueThis.getInfoWindowContent(marker);
		    if (vueThis.currentMidx == idx) {
		      vueThis.infoWinOpen = !vueThis.infoWinOpen;
		    }
		    else {
		      vueThis.infoWinOpen = true;
		      vueThis.currentMidx = idx;
		    }
		}

		},
        getInfoWindowContent: function (marker) {
        	let html = `<article class="single_tovar inside_modalla">
		<div class="heading">
			<div class="left_heading">
				<h3>От ${marker.from_city} до ${marker.to_city}</h3>
				<div class="tooltip"  v-if="marker.fast_payment == 1">
					<span class="material-icons">payments</span>
				</div>
				<div class="tooltip"   v-if="marker.adr == 1">
					<img src="../images/adr.png" alt="ADR">
				</div>
			</div>
			<p class="price">${ marker.price == '0.00' ? 'Няма посочена цена' : marker.price+' лв.' }</p>
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
				<span>${ marker.vehicleName + ' - ' + marker.number_vehicles}</span>
			</p>`
			
			if(marker.weigth !== null && marker.weight !== '0.00') {
				html += `<p class="wrap_icon_text">
					<span class="material-icons">monitor_weight</span>
					<span>${ marker.weight == '0.00' ? 'Няма посочено тегло' : marker.weight + " кг."}</span>
				</p>`
			}
			
			html += `<p class="wrap_icon_text">
				<span class="material-icons">sync_alt</span>
				<span>${ marker.both_directions ? 'Двупосовечен' : 'Еднопосочен' }</span>
			</p>
		</div>`


		html += `<div class="row">
			<p class="wrap_icon_text">
				<span class="material-icons">add_road</span>
				<span>${marker.distance}.</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">schedule</span>
				<span>${marker.time}</span>
			</p>
		</div>`

		if(marker.description !== null) {
			html += `<p class="desc">${marker.description}</p>`;
		}
		html += `</article>`

		return html
	   },
	}
};
</script>
