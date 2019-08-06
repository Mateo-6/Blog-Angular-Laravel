import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../../services/user.service';
import { CategoryService } from '../../services/category.service';
import { PostService } from '../../services/post.service';
import { Post } from '../../models/post';
import { Global } from '../../services/global';

 
@Component({
  selector: 'app-post-edit',
  templateUrl: '../post-new/post-new.component.html',
  styleUrls: ['../post-new/post-new.component.css'],
  providers: [UserService, CategoryService, PostService]
})
export class PostEditComponent implements OnInit {

	public page_title: string;
	public identity;
	public token;
	public post: Post;
	public categories;
	public status;
	public is_edit;
  public url: string;

  public froala_options: Object = {
    charCounterCount: true,
    languaje: 'es',
  }

	public afuConfig = {
	    multiple: false,
	    formatsAllowed: ".jpg, .png, .gif, .jpeg",
	    maxSize: "1",
	    uploadAPI:  {
	      url: Global.url+'post/upload',
	      headers: {
	     	"Authorization" : this._userService.getToken()
	      }
	    },
	    theme: "attachPin",
	    hideProgressBar: false,
	    hideResetBtn: true,
	    hideSelectBtn: false,
	    attachPinText: 'Choose your avatar'
	};

  	constructor(
  		private _route: ActivatedRoute,
  		private _router: Router,
  		private _userService: UserService,
  		private _categoryService: CategoryService,
  		private _postService: PostService
  	) { 

  		this.page_title = "Edit a post";
  		this.identity = this._userService.getIdentity();
  		this.token = this._userService.getToken();
  		this.is_edit = true;
      this.url = Global.url;
  	}

  	ngOnInit() {

  		this.post = new Post(1, this.identity.sub, 1, '', '', '', '');
  		this.getCategories();
  		this.getPost();

  	}

  	getCategories() {

  		this._categoryService.getCategories().subscribe(
  			response => {

  				if(response.status == 'Success') {
  					this.categories = response.categories
  				}

  			},
  			error => {
  				console.log(<any>error);
  			}
  		);

  	}

  	getPost() {
	  	this._route.params.subscribe(params => {
	  		let id = +params['id'];
	  		
	  		this._postService.getPost(id).subscribe(
	  			response => {
	  				if(response.status == 'Success') {
	  					this.post = response.posts;

              if(this.post.user_id != this.identity.sub) {
                this._router.navigate(['/home']);
              }
              
	  				} else {
	  					this._router.navigate(['/home']);
	  				}
	  			}, 
	  			error => {
	  				console.log(<any>error);
	  			}

	  		);

	  	});
	}

  	imageUpload(e) {

  		let data = JSON.parse(e.response);

  		this.post.image = data.image;

  	}

  	onSubmit(form) {
  		
  		this._postService.update(this.token, this.post, this.post.id).subscribe(

  			response => {

  				if(response.status == 'Success') {
  					//this.post = response.post;
  					this.status = 'Success';
  					
  					this._router.navigate(['/post', this.post.id]);

  				} else {
  					
  					this.status = 'Error';
  				
  				}

  			},
  			error => {
  				
  				console.log(<any>error);
  				this.status = 'Error';
  			
  			}

  		);

  	}

}
