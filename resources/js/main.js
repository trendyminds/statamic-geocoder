import Fieldtype from './components/Geocoder.vue'
Statamic.$components.register('geocoder-fieldtype', Fieldtype)

/**
 * Insert the Google Maps script tag into the head of the document
 * and pull the API key from the window object
 */
let gs = document.createElement('script')
gs.setAttribute(
	'src',
	`https://maps.googleapis.com/maps/api/js?key=${window.geocoder_key}&loading=async&libraries=places&callback=initMap`
)
gs.setAttribute('async', true)
document.head.appendChild(gs)
