<template>
	<div>
		<text-input ref="mapfield" :value="value.place" />
		<button
			v-if="value.place"
			class="flex btn btn-xs mt-2 gap-2"
			aria-label="Reset location"
			@click="reset"
		>
			<svg-icon name="eraser" class="h-4" />
			<span>Clear location</span>
		</button>

		<div :class="{ 'sr-only': !value.place }">
			<div ref="googlemap" class="mt-4 rounded-lg" style="height: 500px"></div>
		</div>
	</div>
</template>

<script>
export default {
	mixins: [Fieldtype],

	mounted() {
		// Initialize the Google Maps API
		this.loadGoogleMaps()

		this.autocomplete = null
		this.map = null
		this.marker = null

		// A top-level function that can be called from the Google Maps API once it's loaded
		window.initMap = this.initMap
	},

	methods: {
		loadGoogleMaps() {
			let gs = document.createElement('script')
			gs.setAttribute(
				'src',
				`https://maps.googleapis.com/maps/api/js?key=${window.geocoder_key}&loading=async&libraries=places&callback=initMap`
			)
			gs.setAttribute('async', true)
			document.head.appendChild(gs)
		},

		initMap() {
			// Initialize Places autocomplete
			this.autocomplete = new google.maps.places.Autocomplete(
				this.$refs.mapfield.$refs.input,
				{
					fields: ['address_components', 'geometry', 'icon', 'name'],
				}
			)
			this.autocomplete.addListener('place_changed', this.setPlace)

			// Initialize the map
			this.map = new google.maps.Map(this.$refs.googlemap, {
				zoom: 12,
				center: {
					lat: this.value.lat ?? 39.7684,
					lng: this.value.lng ?? -86.1581,
				},
				mapTypeControl: false,
				fullscreenControl: false,
				streetViewControl: false,
			})

			// Add the marker to the map
			this.marker = new google.maps.Marker({
				draggable: true,
				position: new google.maps.LatLng(this.value.lat, this.value.lng),
				map: this.map,
			})

			// Setup the event listener for the marker
			google.maps.event.addListener(
				this.marker,
				'dragend',
				this.updateMarkerPosition
			)
		},

		setPlace() {
			const place = this.autocomplete.getPlace()
			const streetNumber = this.getMapComponent(place.address_components, [
				'street_number',
			])
			const street = this.getMapComponent(place.address_components, ['route'])
			const city = this.getMapComponent(place.address_components, [
				'locality',
				'political',
			])
			const state = this.getMapComponent(place.address_components, [
				'administrative_area_level_1',
				'political',
			])
			const zip = this.getMapComponent(place.address_components, [
				'postal_code',
			])

			// Change the value of the fieldtype
			this.update({
				name: place.name ?? '',
				place: this.$refs.mapfield.$refs.input.value ?? '',
				lat: place.geometry.location.lat(),
				lng: place.geometry.location.lng(),
				address: `${streetNumber} ${street}`,
				city: city,
				state: state,
				zip: zip,
			})

			// Remove the old marker and add a new one
			this.marker.setMap(null)
			this.marker = new google.maps.Marker({
				draggable: true,
				position: new google.maps.LatLng(
					place.geometry.location.lat(),
					place.geometry.location.lng()
				),
				map: this.map,
			})
			this.map.panTo(this.marker.getPosition())

			// Setup the event listener for the marker
			google.maps.event.addListener(
				this.marker,
				'dragend',
				this.updateMarkerPosition
			)
		},

		updateMarkerPosition() {
			const position = this.marker.getPosition()

			const values = this.value
			values.lat = position.lat()
			values.lng = position.lng()

			this.update(values)
		},

		getMapComponent(address_components, types) {
			const components = address_components.filter(
				(f) => JSON.stringify(f.types) === JSON.stringify(types)
			)

			if (components.length) {
				return components[0].short_name
			}

			return ''
		},

		reset() {
			this.update({
				name: '',
				place: '',
				lat: '',
				lng: '',
				address: '',
				city: '',
				state: '',
				zip: '',
			})
		},
	},

	data() {
		return {
			name: this.value.name,
			place: this.value.place,
			lat: this.value.lat,
			lng: this.value.lng,
			address: this.value.address,
			city: this.value.city,
			state: this.value.state,
			zip: this.value.zip,
		}
	},
}
</script>
