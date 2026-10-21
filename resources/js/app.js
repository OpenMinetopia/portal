import './bootstrap';
import Alpine from 'alpinejs'
import './minecraft-preview'
import brandingEditor from './branding-editor'

window.Alpine = Alpine
Alpine.data('brandingEditor', brandingEditor)
Alpine.start()
