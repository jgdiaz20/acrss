<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Permission;
use App\Role;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('role_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        abort_if(Gate::denies('role_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permissions = Permission::all()->pluck('title', 'id');

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->all());
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.roles.index');
    }

    public function edit(Role $role)
    {
        abort_if(Gate::denies('role_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permissions = Permission::all()->pluck('title', 'id');

        $role->load('permissions');

        return view('admin.roles.edit', compact('permissions', 'role'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->all());
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.roles.index');
    }

    public function show(Role $role)
    {
        abort_if(Gate::denies('role_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role->load('permissions', 'rolesUsers');

        return view('admin.roles.show', compact('role'));
    }

    public function destroy(Role $role)
    {
        abort_if(Gate::denies('role_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roleTitle = $role->title; // Store title before deletion for message
        
        // Hard delete the role - completely remove from database
        $role->forceDelete();

        return back()->with('success', "Role '{$roleTitle}' has been successfully deleted!");
    }

    public function massDestroy(MassDestroyRoleRequest $request)
    {
        // Get role titles before deletion for message
        $roleTitles = Role::whereIn('id', request('ids'))->pluck('title')->toArray();
        $roleCount = count($roleTitles);
        
        // Hard delete multiple roles - completely remove from database
        Role::whereIn('id', request('ids'))->forceDelete();

        if ($roleCount === 1) {
            return response()->json(['message' => "Role '{$roleTitles[0]}' has been successfully deleted!"], Response::HTTP_OK);
        } else {
            return response()->json(['message' => "{$roleCount} roles have been successfully deleted!"], Response::HTTP_OK);
        }
    }
}
