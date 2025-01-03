@extends('user::layouts.master')

@section('content')
<div class="container">
    <h1>User List</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>
                        <a href="{{ route('users.show', $item) }}" class="btn btn-info">View</a>
                        <a href="{{ route('users.edit', $item) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('users.destroy', $item) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No User found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
