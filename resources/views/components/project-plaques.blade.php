@props(['project'])
<div class="plaques">
    <span class="plaque has-moss status-{{ $project->status->value }}"><i class="gem" aria-hidden="true"></i>{{ $project->status->getLabel() }}</span>
    <span class="plaque">{{ $project->type->getLabel() }}</span>
</div>
