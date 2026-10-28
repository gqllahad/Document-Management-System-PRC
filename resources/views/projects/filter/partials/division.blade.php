<select id="divisionFilter" class="filter-select">
    <option value="all">All Divisions</option>
    @foreach ($divisions as $d)
        <option value="{{ $d->id }}">{{ $d->name }}</option>
    @endforeach
</select>