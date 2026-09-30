@props(['project'])
{{-- The featured project rests on the clearing's pedestal, under a ray of light. --}}
<div class="altar">
    <div class="altar-beam" aria-hidden="true"></div>
    <x-project-card :project="$project" />
    <div class="altar-steps" aria-hidden="true"><div class="step"></div><div class="step"></div></div>
</div>
