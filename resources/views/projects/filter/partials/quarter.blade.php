<select id="quarterFilter" class="filter-select">
<option value="all">All Quarters</option>
@foreach ($quarters as $qt)
    <option value="{{ $qt }}">{{ $qt }}</option>
@endforeach
</select>