<!-- resources/views/timezone-converter.blade.php -->
<div class="container">
    <h1>Timezone Converter</h1>

    <form id="converter-form">
        @csrf
        <div>
            <label>Your Datetime:</label>
            <input type="datetime-local" name="datetime" required>
        </div>

        <div>
            <label>Your Timezone:</label>
            <select name="timezone" required>
                @foreach($timezones as $tz)
                    <option value="{{ $tz }}">{{ $tz }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit">Convert</button>
    </form>

    <div id="results" style="margin-top: 20px;"></div>
</div>

<script>
document.getElementById('converter-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    const response = await fetch('/timezone-converter', {
        method: 'POST',
        body: formData
    });

    const data = await response.json();

    let html = '<h3>Converted Times:</h3><ul>';
    for (const [location, time] of Object.entries(data.conversions)) {
        html += `<li><strong>${location}:</strong> ${time}</li>`;
    }
    html += '</ul>';

    document.getElementById('results').innerHTML = html;
});
</script>
