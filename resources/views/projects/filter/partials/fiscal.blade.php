
<select id="fiscalFilter" class="filter-select">
<option value="all">All Fiscal Years</option>
@foreach ($fiscalYears as $fy)
    <option value="{{ $fy }}">{{ $fy }}</option>
@endforeach
</select>