/**
 * New Modal System - Instant, Stable, and Reliable
 * 
 * Features:
 * - Instant display with no delays
 * - Fixed stable dimensions
 * - Data prepared before showing
 * - Backdrop and modal render together
 * - Simple predictable animations
 * - Consistent behavior across all modal types
 * - No race conditions or state conflicts
 * - Responsive and clean layout
 */

class ModalSystem {
    constructor() {
        this.activeModal = null;
        this.modalOverlay = null;
        this.modalContent = null;
        this.currentData = null;
        this.isOpening = false;
        this.isClosing = false;
        
        this.init();
    }

    init() {
        this.createOverlay();
        this.setupEventListeners();
    }

    createOverlay() {
        // Create overlay once and reuse
        this.modalOverlay = document.createElement('div');
        this.modalOverlay.className = 'modal-overlay';
        this.modalOverlay.setAttribute('role', 'dialog');
        this.modalOverlay.setAttribute('aria-modal', 'true');
        this.modalOverlay.setAttribute('aria-hidden', 'true');
        
        document.body.appendChild(this.modalOverlay);
    }

    setupEventListeners() {
        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.activeModal && !this.isClosing) {
                this.close();
            }
        });

        // Close on backdrop click
        this.modalOverlay.addEventListener('click', (e) => {
            if (e.target === this.modalOverlay && !this.isClosing) {
                this.close();
            }
        });

        // Prevent body scroll when modal is open
        this.modalOverlay.addEventListener('wheel', (e) => {
            if (e.target === this.modalOverlay) {
                e.preventDefault();
            }
        }, { passive: false });
    }

    async close() {
        if (this.isClosing || !this.activeModal) {
            return;
        }

        this.isClosing = true;

        // Hide modal immediately
        this.modalOverlay.classList.remove('active');
        
        // Set accessibility attributes
        this.modalOverlay.setAttribute('aria-hidden', 'true');

        // Restore body scroll
        document.body.style.overflow = '';

        // Clear content after animation
        setTimeout(() => {
            this.modalOverlay.innerHTML = '';
            this.modalContent = null;
            this.currentData = null;
            this.activeModal = null;
            this.isClosing = false;
        }, 100);
    }

    async prepareModalData(type, data) {
        switch (type) {
            case 'view':
                return await this.prepareViewData(data);
            case 'edit':
                return await this.prepareEditData(data);
            case 'add':
                return await this.prepareAddData(data);
            case 'delete':
                return await this.prepareDeleteData(data);
            default:
                console.error('Unknown modal type:', type);
                return null;
        }
    }

    async prepareViewData(data) {
        const { observationId, tableName } = data;
        
        try {
            // Fetch observation data
            const response = await fetch(`/api/species-observations/data/${observationId}?table_name=${tableName}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Failed to load observation data');
            }

            return {
                type: 'view',
                observation: result.observation,
                observationId,
                tableName
            };

        } catch (error) {
            console.error('Error preparing view data:', error);
            this.showNotification('Error loading observation data', 'error');
            return null;
        }
    }

    async prepareEditData(data) {
        const { observationId, tableName } = data;
        
        try {
            // Fetch observation data
            const response = await fetch(`/api/species-observations/data/${observationId}?table_name=${tableName}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Failed to load observation data');
            }

            // Get filter options from existing page elements
            const protectedAreasSelect = document.querySelector('#protected_area_id');
            const bioGroupSelect = document.querySelector('#bio_group');
            const yearSelect = document.querySelector('#patrol_year');
            const semesterSelect = document.querySelector('#patrol_semester');
            
            let protectedAreas = [];
            let bioGroups = {};
            let years = [];
            let semesters = {};

            // Extract data from existing selects
            if (protectedAreasSelect) {
                Array.from(protectedAreasSelect.options).forEach(option => {
                    if (option.value) {
                        protectedAreas.push({
                            id: option.value,
                            name: option.textContent,
                            code: option.getAttribute('data-code') || ''
                        });
                    }
                });
            }

            if (bioGroupSelect) {
                Array.from(bioGroupSelect.options).forEach(option => {
                    if (option.value) {
                        bioGroups[option.value] = option.textContent;
                    }
                });
            }

            if (yearSelect) {
                Array.from(yearSelect.options).forEach(option => {
                    if (option.value) {
                        years.push(option.value);
                    }
                });
            }

            if (semesterSelect) {
                Array.from(semesterSelect.options).forEach(option => {
                    if (option.value) {
                        semesters[option.value] = option.textContent;
                    }
                });
            }

            return {
                type: 'edit',
                observation: result.observation,
                protectedAreas,
                bioGroups,
                years,
                semesters,
                observationId,
                tableName,
                originalTableName: result.observation.table_name
            };

        } catch (error) {
            console.error('Error preparing edit data:', error);
            this.showNotification('Error loading observation data', 'error');
            return null;
        }
    }

    async open(type, data = {}) {
        // Prevent multiple simultaneous opens
        if (this.isOpening || this.isClosing) {
            return false;
        }

        this.isOpening = true;

        try {
            // Prepare all data before showing modal
            const preparedData = await this.prepareModalData(type, data);
            
            if (!preparedData) {
                this.isOpening = false;
                return false;
            }

            // Store current data
            this.currentData = preparedData;
            this.activeModal = type;

            // Create modal content with prepared data
            this.modalContent = this.createModalContent(type, preparedData);
            
            // Clear and set new content
            this.modalOverlay.innerHTML = '';
            this.modalOverlay.appendChild(this.modalContent);

            // Set accessibility attributes
            this.modalOverlay.setAttribute('aria-hidden', 'false');

            // Prevent body scroll
            document.body.style.overflow = 'hidden';

            // Show modal instantly (no delays)
            requestAnimationFrame(() => {
                this.modalOverlay.classList.add('active');
                
                // Initialize site name dropdown for edit modal
                if (type === 'edit' && preparedData.observation) {
                    this.initializeEditSiteName(preparedData.observation);
                }
                
                // Initialize site name dropdown for add modal
                if (type === 'add') {
                    this.initializeAddSiteName();
                }
                
                // Focus first focusable element
                this.setFocusTrap();
            });

            this.isOpening = false;
            return true;

        } catch (error) {
            console.error('Error opening modal:', error);
            this.isOpening = false;
            return false;
        }
    }

    async initializeAddSiteName() {
        const protectedAreaSelect = document.querySelector('select[name="protected_area_id"]');
        const siteNameSelect = document.getElementById('modal_site_name');
        
        if (!protectedAreaSelect || !siteNameSelect) {
            return;
        }
        
        // Check if a protected area is already selected
        if (protectedAreaSelect.value) {
            const selectedAreaId = protectedAreaSelect.value;
            
            // Load site names for the selected protected area
            // The dropdown will be enabled only if sites exist
            await this.loadModalSiteNames(selectedAreaId);
        } else {
            // Ensure site name dropdown is disabled by default
            siteNameSelect.disabled = true;
        }
    }

    async initializeEditSiteName(observation) {
        const protectedAreaSelect = document.querySelector('select[name="protected_area_id"]');
        const siteNameSelect = document.getElementById('modal_site_name');
        
        if (!protectedAreaSelect || !siteNameSelect) return;
        
        const selectedAreaId = protectedAreaSelect.value;
        
        if (selectedAreaId) {
            // Load site names and then set the current value
            await this.loadModalSiteNames(selectedAreaId);
            
            // Set the current site name if it exists
            if (observation.site_name_id) {
                siteNameSelect.value = observation.site_name_id;
            }
        } else {
            // Disable site name dropdown for other areas
            siteNameSelect.disabled = true;
        }
    }

    async prepareAddData(data) {
        try {
            // Use the existing filter options from the page
            const protectedAreasSelect = document.querySelector('#protected_area_id');
            const bioGroupSelect = document.querySelector('#bio_group');
            const yearSelect = document.querySelector('#patrol_year');
            const semesterSelect = document.querySelector('#patrol_semester');
            
            let protectedAreas = [];
            let bioGroups = {};
            let years = [];
            let semesters = {};

            // Extract data from existing selects
            if (protectedAreasSelect) {
                Array.from(protectedAreasSelect.options).forEach(option => {
                    if (option.value) {
                        protectedAreas.push({
                            id: option.value,
                            name: option.textContent,
                            code: option.getAttribute('data-code') || ''
                        });
                    }
                });
            }

            if (bioGroupSelect) {
                Array.from(bioGroupSelect.options).forEach(option => {
                    if (option.value) {
                        bioGroups[option.value] = option.textContent;
                    }
                });
            }

            if (yearSelect) {
                Array.from(yearSelect.options).forEach(option => {
                    if (option.value) {
                        years.push(option.value);
                    }
                });
            }

            if (semesterSelect) {
                Array.from(semesterSelect.options).forEach(option => {
                    if (option.value) {
                        semesters[option.value] = option.textContent;
                    }
                });
            }

            return {
                type: 'add',
                protectedAreas,
                bioGroups,
                years,
                semesters
            };

        } catch (error) {
            console.error('Error preparing add data:', error);
            this.showNotification('Error loading form data', 'error');
            return null;
        }
    }

    async prepareDeleteData(data) {
        const { observationId, tableName } = data;
        
        try {
            // Fetch observation data for delete confirmation
            const url = `/api/species-observations/data/${observationId}?table_name=${tableName}`;
            
            const response = await fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Failed to load observation data');
            }

            const returnData = {
                type: 'delete',
                observation: result.observation,
                observationId,
                tableName
            };
            
            return returnData;

        } catch (error) {
            console.error('Error preparing delete data:', error);
            this.showNotification('Error loading observation data', 'error');
            return null;
        }
    }

    createModalContent(type, data) {
        const modal = document.createElement('div');
        modal.className = 'modal-content';

        switch (type) {
            case 'view':
                modal.classList.add('large');
                modal.innerHTML = this.createViewModalHTML(data);
                break;
            case 'edit':
                modal.classList.add('xlarge');
                modal.innerHTML = this.createEditModalHTML(data);
                break;
            case 'add':
                modal.classList.add('xlarge');
                modal.innerHTML = this.createAddModalHTML(data);
                break;
            case 'delete':
                modal.classList.add('small');
                modal.innerHTML = this.createDeleteModalHTML(data);
                break;
        }

        return modal;
    }

    createViewModalHTML(data) {
        const { observation } = data;
        
        const bioGroupBadge = observation.bio_group === 'fauna' 
            ? '<span class="badge fauna">Fauna</span>'
            : '<span class="badge flora">Flora</span>';

        return `
            <div class="modal-header">
                <h2 class="modal-title">View Observation</h2>
                <button class="modal-close" onclick="closeModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="view-grid">
                    <div class="view-item">
                        <div class="view-label">Protected Area</div>
                        <div class="view-value">${observation.protected_area?.name || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Site Name</div>
                        <div class="view-value">${observation.site_name?.name || 'No specific site'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Transaction Code</div>
                        <div class="view-value">${observation.transaction_code || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Station Code</div>
                        <div class="view-value">${observation.station_code || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Patrol Year</div>
                        <div class="view-value">${observation.patrol_year || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Patrol Semester</div>
                        <div class="view-value">${observation.patrol_semester ? observation.patrol_semester + ' Semester' : 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Bio Group</div>
                        <div class="view-value">${bioGroupBadge}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Common Name</div>
                        <div class="view-value">${observation.common_name || 'N/A'}</div>
                    </div>
                    <div class="view-item full-width">
                        <div class="view-label">Scientific Name</div>
                        <div class="view-value"><em>${observation.scientific_name || 'Not specified'}</em></div>
                    </div>
                    <div class="view-item full-width">
                        <div class="view-label">Recorded Count</div>
                        <div class="view-value">${observation.recorded_count || 'N/A'}</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Close</button>
                <button class="btn btn-primary" onclick="modalSystem.editFromView()">Edit</button>
            </div>
        `;
    }

    createEditModalHTML(data) {
        const { observation, protectedAreas, bioGroups, years, semesters } = data;
        
        const protectedAreaOptions = protectedAreas.map(area => 
            `<option value="${area.id}" data-code="${area.code}" ${observation.protected_area_id == area.id ? 'selected' : ''}>${area.name}</option>`
        ).join('');

        const bioGroupOptions = Object.entries(bioGroups).map(([key, value]) => 
            `<option value="${key}" ${observation.bio_group === key ? 'selected' : ''}>${value}</option>`
        ).join('');

        const yearOptions = years.map(year => 
            `<option value="${year}" ${observation.patrol_year == year ? 'selected' : ''}>${year}</option>`
        ).join('');

        const semesterOptions = Object.entries(semesters).map(([key, value]) => 
            `<option value="${key}" ${observation.patrol_semester == key ? 'selected' : ''}>${value}</option>`
        ).join('');

        return `
            <div class="modal-header">
                <h2 class="modal-title">Edit Observation</h2>
                <button class="modal-close" onclick="closeModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form class="modal-form" onsubmit="modalSystem.submitEditForm(event, ${observation.id}, '${observation.table_name}')">
                <div class="modal-body">
                    <input type="hidden" name="observation_id" value="${observation.id}">
                    <input type="hidden" name="table_name" value="${observation.table_name || ''}">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Protected Area</label>
                            <select class="form-select" name="protected_area_id" required onchange="modalSystem.handleProtectedAreaChange(this)">
                                <option value="">Select Protected Area</option>
                                ${protectedAreaOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Site Name</label>
                            <select class="form-select" name="site_name_id" id="modal_site_name">
                                <option value="">No specific site</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Transaction Code</label>
                            <input type="text" class="form-input" name="transaction_code" value="${observation.transaction_code || ''}" required maxlength="50">
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Station Code</label>
                            <input type="text" class="form-input" name="station_code" value="${observation.station_code || ''}" required maxlength="60">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Patrol Year</label>
                            <select class="form-select" name="patrol_year" required>
                                <option value="">Select Year</option>
                                ${yearOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Patrol Semester</label>
                            <select class="form-select" name="patrol_semester" required>
                                <option value="">Select Semester</option>
                                ${semesterOptions}
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Bio Group</label>
                            <select class="form-select" name="bio_group" required>
                                <option value="">Select Bio Group</option>
                                ${bioGroupOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Common Name</label>
                            <input type="text" class="form-input" name="common_name" value="${observation.common_name || ''}" required maxlength="150">
                        </div>
                    </div>
                    
                    <div class="form-row single">
                        <div class="form-group">
                            <label class="form-label">Scientific Name</label>
                            <input type="text" class="form-input" name="scientific_name" value="${observation.scientific_name || ''}" maxlength="200">
                        </div>
                    </div>
                    
                    <div class="form-row single">
                        <div class="form-group">
                            <label class="form-label required">Recorded Count</label>
                            <input type="number" class="form-input" name="recorded_count" value="${observation.recorded_count || ''}" required min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        `;
    }

    createAddModalHTML(data) {
        const { protectedAreas, bioGroups, years, semesters } = data;
        
        const protectedAreaOptions = protectedAreas.map(area => 
            `<option value="${area.id}" data-code="${area.code}">${area.name}</option>`
        ).join('');

        const bioGroupOptions = Object.entries(bioGroups).map(([key, value]) => 
            `<option value="${key}">${value}</option>`
        ).join('');

        const yearOptions = years.map(year => 
            `<option value="${year}">${year}</option>`
        ).join('');

        const semesterOptions = Object.entries(semesters).map(([key, value]) => 
            `<option value="${key}">${value}</option>`
        ).join('');

        return `
            <div class="modal-header">
                <h2 class="modal-title">Add New Observation</h2>
                <button class="modal-close" onclick="closeModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form class="modal-form" onsubmit="modalSystem.submitAddForm(event)">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Protected Area</label>
                            <select class="form-select" name="protected_area_id" required onchange="modalSystem.handleProtectedAreaChange(this)">
                                <option value="">Select Protected Area</option>
                                ${protectedAreaOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Site Name</label>
                            <select class="form-select" name="site_name_id" id="modal_site_name">
                                <option value="">No specific site</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Transaction Code</label>
                            <input type="text" class="form-input" name="transaction_code" required maxlength="50" placeholder="e.g., PPLS-2024-001">
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Station Code</label>
                            <input type="text" class="form-input" name="station_code" required maxlength="60" placeholder="e.g., STATION-001">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Patrol Year</label>
                            <select class="form-select" name="patrol_year" required>
                                <option value="">Select Year</option>
                                ${yearOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Patrol Semester</label>
                            <select class="form-select" name="patrol_semester" required>
                                <option value="">Select Semester</option>
                                ${semesterOptions}
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Bio Group</label>
                            <select class="form-select" name="bio_group" required>
                                <option value="">Select Bio Group</option>
                                ${bioGroupOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Common Name</label>
                            <input type="text" class="form-input" name="common_name" required maxlength="150" placeholder="e.g., Philippine Eagle">
                        </div>
                    </div>
                    
                    <div class="form-row single">
                        <div class="form-group">
                            <label class="form-label">Scientific Name</label>
                            <input type="text" class="form-input" name="scientific_name" maxlength="200" placeholder="e.g., Pithecophaga jefferyi">
                        </div>
                    </div>
                    
                    <div class="form-row single">
                        <div class="form-group">
                            <label class="form-label required">Recorded Count</label>
                            <input type="number" class="form-input" name="recorded_count" required min="0" placeholder="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Observation</button>
                </div>
            </form>
        `;
    }

    async handleProtectedAreaChange(selectElement) {
        const siteNameSelect = document.getElementById('modal_site_name');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const areaCode = selectedOption.getAttribute('data-code');
        const selectedAreaId = selectElement.value;
        
        if (!siteNameSelect) {
            console.error('Site name select not found!');
            return;
        }
        
        // Clear current site name options and disable by default
        siteNameSelect.innerHTML = '<option value="">No specific site</option>';
        siteNameSelect.disabled = true;
        
        if (selectedAreaId) {
            // Load site names for the selected protected area
            // The API will return empty array if no sites exist, keeping dropdown disabled
            await this.loadModalSiteNames(selectedAreaId);
        }
    }

    async loadModalSiteNames(protectedAreaId) {
        const siteNameSelect = document.getElementById('modal_site_name');
        
        if (!siteNameSelect) {
            console.error('Site name select element not found!');
            return;
        }
        
        try {
            // Use the correct API route with /api prefix
            const url = `/api/species-observations/site-names/${protectedAreaId}`;
            
            const response = await fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const siteNames = await response.json();
            
            // Create a document fragment for efficient DOM manipulation
            const fragment = document.createDocumentFragment();
            
            // Add default option
            const noSpecificSiteOption = document.createElement('option');
            noSpecificSiteOption.value = '';
            noSpecificSiteOption.textContent = 'No specific site';
            fragment.appendChild(noSpecificSiteOption);
            
            // Add site name options only if sites exist
            if (siteNames && siteNames.length > 0) {
                siteNames.forEach(siteName => {
                    const option = document.createElement('option');
                    option.value = siteName.id;
                    option.textContent = siteName.name;
                    fragment.appendChild(option);
                });
                
                // Enable dropdown only if sites exist
                siteNameSelect.disabled = false;
            } else {
                // Keep dropdown disabled if no sites exist
                siteNameSelect.disabled = true;
            }
            
            // Clear existing options and add new ones
            siteNameSelect.innerHTML = '';
            siteNameSelect.appendChild(fragment);
            
        } catch (error) {
            console.error('Error loading site names:', error);
            // Keep dropdown disabled on error
            siteNameSelect.disabled = true;
        }
    }

    getProtectedAreaOptions(selectedId = null) {
        // This should be populated from the actual data
        // For now, returning empty - will be populated in prepareAddData
        return '';
    }

    getYearOptions(selectedYear = null) {
        const currentYear = new Date().getFullYear();
        const years = [];
        for (let year = currentYear + 1; year >= currentYear - 10; year--) {
            years.push(`<option value="${year}" ${year == selectedYear ? 'selected' : ''}>${year}</option>`);
        }
        return years.join('');
    }

    async submitEditForm(event, observationId, tableName) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.textContent = 'Saving...';

        try {
            // Add _method field to simulate PUT request for Laravel
            const formDataObj = Object.fromEntries(formData);
            formDataObj._method = 'PUT';
            
            // Use the original table_name if available, otherwise determine from protected area
            if (this.currentData && this.currentData.originalTableName) {
                formDataObj.table_name = this.currentData.originalTableName;
            } else if (!formDataObj.table_name || formDataObj.table_name === '') {
                // Determine table_name based on protected area
                const protectedAreaId = formDataObj.protected_area_id;
                let tableName = 'bms_species_observations'; // default
                
                // Map protected area IDs to table names
                const areaTableMap = {
                    '1': 'bangan_observations',      // Bangan Hill National Park
                    '2': 'baua_observations',       // Baua
                    '3': 'casecnan_observations',   // Casecnan
                    '4': 'dipaniong_observations',  // Dipaniong
                    '5': 'dupax_observations',      // Dupax
                };
                
                tableName = areaTableMap[protectedAreaId] || 'bms_species_observations';
                formDataObj.table_name = tableName;
            }
            
            const response = await fetch(`/species-observations/${observationId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formDataObj)
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Observation updated successfully!', 'success');
                this.close();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                // Show detailed error messages if available
                let errorMessage = result.message || 'Failed to update observation';
                
                if (result.errors) {
                    const errorMessages = Object.values(result.errors).flat();
                    errorMessage = errorMessages.join(', ');
                }
                
                this.showNotification(errorMessage, 'error');
                console.error('Validation errors:', result.errors);
            }
        } catch (error) {
            console.error('Error updating observation:', error);
            this.showNotification('Error updating observation', 'error');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.textContent = 'Save Changes';
        }
    }

    async submitAddForm(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.textContent = 'Saving...';

        try {
            const response = await fetch(window.routes.speciesObservationsStore, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Observation added successfully!', 'success');
                this.close();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                this.showNotification(result.message || 'Failed to add observation', 'error');
            }
        } catch (error) {
            console.error('Error adding observation:', error);
            this.showNotification('Error adding observation', 'error');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.textContent = 'Save Observation';
        }
    }

    editFromView() {
        if (this.currentData && this.currentData.observationId) {
            this.close();
            setTimeout(() => {
                this.open('edit', {
                    observationId: this.currentData.observationId,
                    tableName: this.currentData.tableName
                });
            }, 150);
        }
    }

    setFocusTrap() {
        if (!this.modalContent) return;

        const focusableElements = this.modalContent.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        if (focusableElements.length > 0) {
            focusableElements[0].focus();
        }
    }

    createDeleteModalHTML(data) {
        const { observation } = data;
        
        return `
            <div class="modal-header">
                <h2 class="modal-title">Delete Observation</h2>
                <button class="modal-close" onclick="window.closeModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div style="text-align: center; padding: 1rem 0;">
                    <div style="width: 36px; height: 36px; margin: 0 auto 0.5rem; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 style="margin: 0 0 0.5rem 0; font-size: 0.9rem; font-weight: 600; color: #111827;">Delete ${observation.common_name || 'this item'}?</h3>
                    <p style="margin: 0; color: #ef4444; font-size: 0.7rem; font-weight: 500;">Cannot be undone</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="window.closeModal()">Cancel</button>
                <button class="btn btn-danger" onclick="window.modalSystem.confirmDelete(${observation.id}, '${observation.table_name || ''}')">Delete</button>
            </div>
        `;
    }

    async confirmDelete(observationId, tableName) {
        try {
            const response = await fetch(window.routes.speciesObservationsDestroy.replace(':id', observationId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    table_name: tableName
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Observation deleted successfully!', 'success');
                this.close();
                
                // Remove the row from the table instantly
                this.removeObservationRow(observationId);
            } else {
                this.showNotification(result.message || result.error || 'Failed to delete observation', 'error');
            }
        } catch (error) {
            this.showNotification('Error deleting observation', 'error');
        }
    }

    removeObservationRow(observationId) {
        // Find and remove the row from the table using a more reliable method
        const rows = document.querySelectorAll('.observation-row');
        let rowRemoved = false;
        
        rows.forEach(row => {
            // Look for the delete button with the specific observation ID
            // Check both old and new patterns for compatibility
            const deleteBtn = row.querySelector(`button[onclick*="window.openDeleteModal(${observationId}"]`) ||
                          row.querySelector(`button[onclick*="openDeleteModal(${observationId}"]`);
            if (deleteBtn) {
                row.remove();
                rowRemoved = true;
                
                // Update the count if it exists
                const countElement = document.querySelector('h2');
                if (countElement) {
                    const countText = countElement.textContent;
                    const countMatch = countText.match(/\((\d+)\s+records\)/);
                    if (countMatch) {
                        const currentCount = parseInt(countMatch[1]);
                        const newCount = Math.max(0, currentCount - 1);
                        countElement.textContent = countText.replace(/\((\d+)\s+records\)/, `(${newCount} records)`);
                    }
                }
            }
        });
        
        if (!rowRemoved) {
            console.warn(`Could not find row for observation ID: ${observationId}`);
        }
    }

    showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 10000;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            font-weight: 500;
            max-width: 400px;
            transform: translateX(100%);
            transition: transform 0.2s ease;
        `;

        if (type === 'success') {
            notification.style.backgroundColor = '#10b981';
            notification.style.color = 'white';
        } else if (type === 'error') {
            notification.style.backgroundColor = '#ef4444';
            notification.style.color = 'white';
        }

        notification.textContent = message;
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);

        // Auto remove
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 200);
        }, 3000);
    }
}

// Initialize the modal system
let modalSystem;

// Wait for DOM to be ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeModalSystem);
} else {
    initializeModalSystem();
}

function initializeModalSystem() {
    try {
        modalSystem = new ModalSystem();
        
        // Make sure it's available globally
        window.modalSystem = modalSystem;
        
        // Global functions for onclick handlers
        window.openViewModal = (observationId, tableName) => {
            modalSystem.open('view', { observationId, tableName });
        };

        window.openEditModal = (observationId, tableName) => {
            modalSystem.open('edit', { observationId, tableName });
        };

        window.openAddModal = () => {
            modalSystem.open('add', {});
        };

        window.openDeleteModal = (observationId, tableName) => {
            modalSystem.open('delete', { observationId, tableName });
        };

        window.closeModal = () => {
            modalSystem.close();
        };
        
    } catch (error) {
        console.error('Error initializing ModalSystem:', error);
    }
}
