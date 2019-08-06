import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../../services/user.service';
import { CategoryService } from '../../services/category.service';
import { Category } from '../../models/category'; 

@Component({
  selector: 'app-category-new',
  templateUrl: './category-new.component.html',
  styleUrls: ['./category-new.component.css'],
  providers: [UserService, CategoryService]
})
export class CategoryNewComponent implements OnInit {

	public page_title: string;
	public indentity;
	public token;
	public category: Category;
	public message: string;
	public status: string;

	constructor(
		private _route: ActivatedRoute,
		private _router: Router,
		private _userService: UserService,
		private _categoryService: CategoryService
	) {

		this.page_title = "New category";
		this.indentity = this._userService.getIdentity();
		this.token = this._userService.getToken();
		this.category = new Category(1, '');

	}

	ngOnInit() {
	}

	onSubmit(form) {
		
		this._categoryService.create(this.token, this.category).subscribe(

			response => {

				if(response.status == 'success') {

					this.category = response.category;
					this.status = 'success';
					this.message = response.message;

					//this._router.navigate(['/home'])
					form.reset();

				} else {

					this.status = 'error'
					this.message = response.message;
					console.log(response);

				}

			},
			error => {

				console.log(<any>error);
				this.status = 'error'
				this.message = error.error.message;

			} 

		);

	}

}
