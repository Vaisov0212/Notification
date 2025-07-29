<style>
    .time-input-group {
        margin-bottom: 10px;
    }
    .monthly-checkboxes {
        max-height: 200px;
        overflow-y: auto;
    }
    .day-checkbox {
        margin-right: 15px;
        margin-bottom: 10px;
    }
    .color-preview {
        width: 30px;
        height: 30px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        display: inline-block;
        margin-left: 10px;
    }
</style>
@stack('style')

@include('layouts.header')

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="container my-5">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white">
                                <h3 class="card-title mb-0">Deployment Strategy Configuration</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('dashboard.events.store') }}" method="POST">
                                    @csrf
                                    <div style="padding-left: 30px;">
                                        <!-- Title -->
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Strategy Title</label>
                                            <input type="text" name="title" class="form-control" id="title" placeholder="Enter deployment strategy title" required>
                                        </div>

                                        <!-- Color -->
                                        <div class="mb-3">
                                            <label for="color" class="form-label">Color Theme</label>
                                            <div class="d-flex align-items-center">
                                                <input type="color" name="color" class="form-control form-control-color" id="color" value="#0d6efd" title="Choose color">
                                                <span class="color-preview ms-2" id="colorPreview" style="background-color: #0d6efd;"></span>
                                                <span class="ms-2" id="colorValue">#0d6efd</span>
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea name="description" class="form-control" id="description" rows="3" placeholder="Describe your deployment strategy" required></textarea>
                                        </div>

                                        <!-- Frequency -->
                                        <div class="mb-3">
                                            <label for="frequency" class="form-label">Deployment Frequency</label>
                                            <select class="form-select" name="repeat_interval" id="frequency" onchange="toggleFrequencyOptions()">
                                                <option value="daily">Daily</option>
                                                <option value="weekly">Weekly</option>
                                                <option value="monthly">Monthly</option>
                                            </select>
                                        </div>

                                        <!-- Daily Options -->
                                        <div class="mb-3" id="dailyOptions">
                                            <label for="dailyInterval" class="form-label">Every N Days</label>
                                            <input type="number" name="repeat_days" class="form-control" id="dailyInterval" min="1" >
                                        </div>

                                        <!-- Weekly Options -->
                                        <div class="mb-3" id="weeklyOptions" style="display: none;">
                                            <label class="form-label">Days of the Week</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check day-checkbox">
                                                        <input name="weekly_days[]" class="form-check-input" type="checkbox" value="monday" id="mon">
                                                        <label class="form-check-label" for="mon">Monday</label>
                                                    </div>
                                                    <div class="form-check day-checkbox">
                                                        <input name="weekly_days[]" class="form-check-input" type="checkbox" value="tuesday" id="tue">
                                                        <label class="form-check-label" for="tue">Tuesday</label>
                                                    </div>
                                                    <div class="form-check day-checkbox">
                                                        <input name="weekly_days[]" class="form-check-input" type="checkbox" value="wednesday" id="wed">
                                                        <label class="form-check-label" for="wed">Wednesday</label>
                                                    </div>
                                                    <div class="form-check day-checkbox">
                                                        <input name="weekly_days[]" class="form-check-input" type="checkbox" value="thursday" id="thu">
                                                        <label class="form-check-label" for="thu">Thursday</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check day-checkbox">
                                                        <input name="weekly_days[]" class="form-check-input" type="checkbox" value="friday" id="fri">
                                                        <label class="form-check-label" for="fri">Friday</label>
                                                    </div>
                                                    <div class="form-check day-checkbox">
                                                        <input name="weekly_days[]" class="form-check-input" type="checkbox" value="saturday" id="sat">
                                                        <label class="form-check-label" for="sat">Saturday</label>
                                                    </div>
                                                    <div class="form-check day-checkbox">
                                                        <input name="weekly_days[]" class="form-check-input" type="checkbox" value="sunday" id="sun">
                                                        <label class="form-check-label" for="sun">Sunday</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Monthly Options -->
                                        <div class="mb-3" id="monthlyOptions" style="display: none;">
                                            <label class="form-label">Days of the Month</label>
                                            <div class="monthly-checkboxes border rounded p-3">
                                                <div class="row" id="monthlyDaysContainer">
                                                    <!-- Generated by JavaScript -->
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Event Times -->
                                        <div class="mb-3">
                                            <label class="form-label">Event Times</label>
                                            <div id="eventTimesContainer">
                                                <div class="time-input-group d-flex align-items-center">
                                                    <select name="event_times[]" class="form-select me-2">
                                                        <!-- Generated by JavaScript -->
                                                    </select>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeTimeInput(this)" style="display: none;">Remove</button>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-success btn-sm mt-2" onclick="addTimeInput()">Add Time</button>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                                Save Deployment Strategy
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <!-- Preview -->
                                <div class="mt-4">
                                    <h5>Preview</h5>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div id="previewContent">
                                                <p><strong>Title:</strong> <span id="previewTitle">Untitled</span></p>
                                                <p><strong>Color:</strong> <span id="previewColor" class="color-preview me-2"></span><span id="previewColorValue">#0d6efd</span></p>
                                                <p><strong>Description:</strong> <span id="previewDescription">No description</span></p>
                                                <p><strong>Frequency:</strong> <span id="previewFrequency">daily</span></p>
                                                <p id="previewFrequencyDetails"><strong>Interval:</strong> Every 1 day(s)</p>
                                                <p><strong>Times:</strong> <span id="previewTimes">09:00</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

@include('layouts.footer')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize time options
    function initializeTimeOptions() {
        const timeOptions = [];
        for (let hour = 0; hour < 24; hour++) {
            timeOptions.push(`${hour.toString().padStart(2, '0')}:00`);
            timeOptions.push(`${hour.toString().padStart(2, '0')}:30`);
        }

        // Add options to all time selects
        document.querySelectorAll('select[name="event_times[]"]').forEach(select => {
            select.innerHTML = '';
            timeOptions.forEach(time => {
                const option = document.createElement('option');
                option.value = time;
                option.textContent = time;
                if (time === '09:00') option.selected = true;
                select.appendChild(option);
            });
        });
    }

    // Initialize monthly days
    function initializeMonthlyDays() {
        const container = document.getElementById('monthlyDaysContainer');
        container.innerHTML = '';

        for (let day = 1; day <= 31; day++) {
            const col = document.createElement('div');
            col.className = 'col-2 mb-2';
            col.style = "padding-left: 35px;";

            const formCheck = document.createElement('div');
            formCheck.className = 'form-check';

            const input = document.createElement('input');
            input.className = 'form-check-input';
            input.type = 'checkbox';
            input.value = day;
            input.id = `day${day}`;
            input.name = 'monthly_days[]';

            const label = document.createElement('label');
            label.className = 'form-check-label';
            label.setAttribute('for', `day${day}`);
            label.textContent = day;

            formCheck.appendChild(input);
            formCheck.appendChild(label);
            col.appendChild(formCheck);
            container.appendChild(col);
        }
    }

    // Toggle frequency options
    function toggleFrequencyOptions() {
        const frequency = document.getElementById('frequency').value;

        document.getElementById('dailyOptions').style.display = frequency === 'daily' ? 'block' : 'none';
        document.getElementById('weeklyOptions').style.display = frequency === 'weekly' ? 'block' : 'none';
        document.getElementById('monthlyOptions').style.display = frequency === 'monthly' ? 'block' : 'none';

        updatePreview();
    }

    // Add time input
    function addTimeInput() {
        const container = document.getElementById('eventTimesContainer');
        const newTimeGroup = document.createElement('div');
        newTimeGroup.className = 'time-input-group d-flex align-items-center';

        newTimeGroup.innerHTML = `
            <select class="form-select me-2" name="event_times[]">
                <!-- Will be populated by initializeTimeOptions -->
            </select>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeTimeInput(this)">Remove</button>
        `;

        container.appendChild(newTimeGroup);
        initializeTimeOptions();
        updateRemoveButtons();
        updatePreview();
    }

    // Remove time input
    function removeTimeInput(button) {
        button.parentElement.remove();
        updateRemoveButtons();
        updatePreview();
    }

    // Update remove buttons visibility
    function updateRemoveButtons() {
        const timeGroups = document.querySelectorAll('.time-input-group');
        timeGroups.forEach((group, index) => {
            const removeBtn = group.querySelector('.btn-danger');
            if (removeBtn) {
                removeBtn.style.display = timeGroups.length > 1 ? 'inline-block' : 'none';
            }
        });
    }

    // Update color preview
    function updateColorPreview() {
        const color = document.getElementById('color').value;
        document.getElementById('colorPreview').style.backgroundColor = color;
        document.getElementById('colorValue').textContent = color;
        document.getElementById('submitBtn').style.backgroundColor = color;
        updatePreview();
    }

    // Update preview
    function updatePreview() {
        const title = document.getElementById('title').value || 'Untitled';
        const color = document.getElementById('color').value;
        const description = document.getElementById('description').value || 'No description';
        const frequency = document.getElementById('frequency').value;

        document.getElementById('previewTitle').textContent = title;
        document.getElementById('previewColor').style.backgroundColor = color;
        document.getElementById('previewColorValue').textContent = color;
        document.getElementById('previewDescription').textContent = description;
        document.getElementById('previewFrequency').textContent = frequency;

        // Update frequency details
        let frequencyDetails = '';
        if (frequency === 'daily') {
            const interval = document.getElementById('dailyInterval').value;
            frequencyDetails = `<strong>Interval:</strong> Every ${interval} day(s)`;
        } else if (frequency === 'weekly') {
            const selectedDays = Array.from(document.querySelectorAll('#weeklyOptions input:checked')).map(cb => cb.value);
            frequencyDetails = `<strong>Days:</strong> ${selectedDays.join(', ') || 'None selected'}`;
        } else if (frequency === 'monthly') {
            const selectedDays = Array.from(document.querySelectorAll('#monthlyOptions input:checked')).map(cb => cb.value);
            frequencyDetails = `<strong>Days:</strong> ${selectedDays.join(', ') || 'None selected'}`;
        }
        document.getElementById('previewFrequencyDetails').innerHTML = frequencyDetails;

        // Update times
        const times = Array.from(document.querySelectorAll('select[name="event_times[]"]')).map(select => select.value);
        document.getElementById('previewTimes').textContent = times.join(', ');
    }

    // Event listeners
    document.getElementById('color').addEventListener('change', updateColorPreview);
    document.getElementById('title').addEventListener('input', updatePreview);
    document.getElementById('description').addEventListener('input', updatePreview);
    document.getElementById('dailyInterval').addEventListener('input', updatePreview);

    // Add event listeners for checkboxes and selects
    document.addEventListener('change', function(e) {
        if (e.target.matches('#weeklyOptions input[type="checkbox"]') ||
            e.target.matches('#monthlyOptions input[type="checkbox"]') ||
            e.target.matches('select[name="event_times[]"]')) {
            updatePreview();
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeTimeOptions();
        initializeMonthlyDays();
        updateColorPreview();
        updateRemoveButtons();
        updatePreview();
    });
</script>

@stack('script')
